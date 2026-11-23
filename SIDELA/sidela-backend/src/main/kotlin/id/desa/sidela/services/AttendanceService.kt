package id.desa.sidela.services

import id.desa.sidela.database.*
import id.desa.sidela.models.*
import id.desa.sidela.utils.FileUploadHelper
import id.desa.sidela.utils.GeoLocationUtil
import org.jetbrains.exposed.sql.*
import java.math.BigDecimal
import java.time.Duration
import java.time.LocalDate
import java.time.LocalTime
import java.time.format.DateTimeFormatter
import java.time.format.TextStyle
import java.util.*

object AttendanceService {

    private val timeFormatter = DateTimeFormatter.ofPattern("HH:mm:ss")
    private val displayTimeFormatter = DateTimeFormatter.ofPattern("HH:mm")

    suspend fun getSettings(): AttendanceSettingDTO {
        return DatabaseFactory.dbQuery {
            val row = AttendanceSettingsTable.selectAll().limit(1).singleOrNull()
            if (row != null) {
                AttendanceSettingDTO(
                    id = row[AttendanceSettingsTable.id],
                    officeName = row[AttendanceSettingsTable.officeName],
                    latitude = row[AttendanceSettingsTable.latitude].toDouble(),
                    longitude = row[AttendanceSettingsTable.longitude].toDouble(),
                    radiusMeters = row[AttendanceSettingsTable.radiusMeters],
                    workStartTime = row[AttendanceSettingsTable.workStartTime].format(displayTimeFormatter),
                    workEndTime = row[AttendanceSettingsTable.workEndTime].format(displayTimeFormatter),
                    lateToleranceMinutes = row[AttendanceSettingsTable.lateToleranceMinutes],
                    breakStartTime = row[AttendanceSettingsTable.breakStartTime].format(displayTimeFormatter),
                    breakEndTime = row[AttendanceSettingsTable.breakEndTime].format(displayTimeFormatter),
                    isActive = row[AttendanceSettingsTable.isActive]
                )
            } else {
                AttendanceSettingDTO(
                    id = 1,
                    officeName = "Kantor Pemerintah Desa Iraonogela",
                    latitude = 1.0425000,
                    longitude = 97.5258000,
                    radiusMeters = 50,
                    workStartTime = "08:00",
                    workEndTime = "16:00",
                    lateToleranceMinutes = 15,
                    breakStartTime = "12:00",
                    breakEndTime = "13:00",
                    isActive = true
                )
            }
        }
    }

    suspend fun getTodaySummary(userId: Long): TodayAttendanceSummary {
        val today = LocalDate.now()
        val settings = getSettings()

        return DatabaseFactory.dbQuery {
            val record = AttendanceTable.select {
                (AttendanceTable.userId eq userId) and (AttendanceTable.date eq today)
            }.singleOrNull()

            val dayNameIndo = today.dayOfWeek.getDisplayName(TextStyle.FULL, Locale("id", "ID"))
            val nowTime = LocalTime.now().format(displayTimeFormatter)

            val clockInStr = record?.get(AttendanceTable.clockIn)?.format(displayTimeFormatter) ?: "--"
            val clockOutStr = record?.get(AttendanceTable.clockOut)?.format(displayTimeFormatter) ?: "--"

            val totalWorkMins = record?.get(AttendanceTable.totalWorkMinutes) ?: 0
            val workHoursFormatted = if (totalWorkMins > 0) {
                val h = totalWorkMins / 60
                val m = totalWorkMins % 60
                "${h}h ${m}m"
            } else "--"

            val statusToday = record?.get(AttendanceTable.status) ?: "BELUM_HADIR"

            TodayAttendanceSummary(
                date = today.toString(),
                dayName = dayNameIndo,
                clockIn = clockInStr,
                clockOut = clockOutStr,
                workHours = workHoursFormatted,
                breakTime = "1h",
                statusToday = statusToday,
                isClockInAllowed = record == null,
                isClockOutAllowed = record != null && record[AttendanceTable.clockOut] == null,
                serverTime = nowTime,
                officeRadiusMeters = settings.radiusMeters,
                officeLatitude = settings.latitude,
                officeLongitude = settings.longitude
            )
        }
    }

    suspend fun clockIn(userId: Long, req: ClockInRequest): AttendanceResponse {
        val settings = getSettings()
        val today = LocalDate.now()
        val nowTime = LocalTime.now()

        // 1. GPS Validation
        val (isWithinRadius, distance) = GeoLocationUtil.isWithinRadius(
            userLat = req.latitude,
            userLon = req.longitude,
            officeLat = settings.latitude,
            officeLon = settings.longitude,
            allowedRadiusMeters = settings.radiusMeters
        )

        if (!isWithinRadius) {
            throw IllegalArgumentException("Lokasi berada di luar radius kantor desa (${"%.1f".format(distance)} meter). Batas radius adalah ${settings.radiusMeters} meter.")
        }

        // 2. Biometric Face Verification
        val verifyResult = FaceRecognitionService.verifyFace(userId, req.faceEmbedding, req.livenessScore)
        if (!verifyResult.isMatched) {
            throw IllegalArgumentException(verifyResult.message)
        }

        val photoUrl = req.photoBase64?.let {
            FileUploadHelper.saveBase64Image(it, "clock_in_${userId}")
        }

        // 3. Late Calculation
        val officialStart = LocalTime.parse(settings.workStartTime)
        val toleranceMinutes = settings.lateToleranceMinutes
        val maxAllowedStart = officialStart.plusMinutes(toleranceMinutes.toLong())

        val lateMinutes = if (nowTime.isAfter(maxAllowedStart)) {
            Duration.between(officialStart, nowTime).toMinutes().toInt()
        } else {
            0
        }

        val status = if (lateMinutes > 0) "TERLAMBAT" else "HADIR"

        return DatabaseFactory.dbQuery {
            val existing = AttendanceTable.select {
                (AttendanceTable.userId eq userId) and (AttendanceTable.date eq today)
            }.singleOrNull()

            if (existing != null) {
                throw IllegalStateException("Anda sudah melakukan presensi masuk untuk hari ini pada pukul ${existing[AttendanceTable.clockIn]?.format(displayTimeFormatter)}.")
            }

            val insertedId = AttendanceTable.insert {
                it[AttendanceTable.userId] = userId
                it[date] = today
                it[clockIn] = nowTime
                it[clockInLat] = BigDecimal.valueOf(req.latitude)
                it[clockInLon] = BigDecimal.valueOf(req.longitude)
                it[clockInDistanceMeters] = BigDecimal.valueOf(distance)
                it[clockInPhotoUrl] = photoUrl
                it[faceConfidenceIn] = BigDecimal.valueOf(verifyResult.confidencePercentage)
                it[livenessPassedIn] = verifyResult.livenessPassed
                it[AttendanceTable.status] = status
                it[AttendanceTable.lateMinutes] = lateMinutes
                it[earlyLeaveMinutes] = 0
                it[totalWorkMinutes] = 0
                it[verificationMethod] = "FACE_RECOGNITION"
                it[notes] = req.notes
            } get AttendanceTable.id

            buildAttendanceResponse(insertedId)
        }
    }

    suspend fun clockOut(userId: Long, req: ClockOutRequest): AttendanceResponse {
        val settings = getSettings()
        val today = LocalDate.now()
        val nowTime = LocalTime.now()

        // 1. GPS Validation
        val (isWithinRadius, distance) = GeoLocationUtil.isWithinRadius(
            userLat = req.latitude,
            userLon = req.longitude,
            officeLat = settings.latitude,
            officeLon = settings.longitude,
            allowedRadiusMeters = settings.radiusMeters
        )

        if (!isWithinRadius) {
            throw IllegalArgumentException("Lokasi berada di luar radius kantor desa (${"%.1f".format(distance)} meter).")
        }

        // 2. Face Verification
        val verifyResult = FaceRecognitionService.verifyFace(userId, req.faceEmbedding, req.livenessScore)
        if (!verifyResult.isMatched) {
            throw IllegalArgumentException(verifyResult.message)
        }

        val photoUrl = req.photoBase64?.let {
            FileUploadHelper.saveBase64Image(it, "clock_out_${userId}")
        }

        val officialEnd = LocalTime.parse(settings.workEndTime)
        val earlyLeaveMinutes = if (nowTime.isBefore(officialEnd)) {
            Duration.between(nowTime, officialEnd).toMinutes().toInt()
        } else {
            0
        }

        return DatabaseFactory.dbQuery {
            val record = AttendanceTable.select {
                (AttendanceTable.userId eq userId) and (AttendanceTable.date eq today)
            }.singleOrNull() ?: throw IllegalStateException("Anda belum melakukan presensi masuk hari ini.")

            if (record[AttendanceTable.clockOut] != null) {
                throw IllegalStateException("Anda sudah melakukan presensi pulang hari ini.")
            }

            val clockInTime = record[AttendanceTable.clockIn] ?: nowTime
            val totalMins = Duration.between(clockInTime, nowTime).toMinutes().toInt().coerceAtLeast(0)

            AttendanceTable.update({ (AttendanceTable.userId eq userId) and (AttendanceTable.date eq today) }) {
                it[clockOut] = nowTime
                it[clockOutLat] = BigDecimal.valueOf(req.latitude)
                it[clockOutLon] = BigDecimal.valueOf(req.longitude)
                it[clockOutDistanceMeters] = BigDecimal.valueOf(distance)
                it[clockOutPhotoUrl] = photoUrl
                it[faceConfidenceOut] = BigDecimal.valueOf(verifyResult.confidencePercentage)
                it[livenessPassedOut] = verifyResult.livenessPassed
                it[AttendanceTable.earlyLeaveMinutes] = earlyLeaveMinutes
                it[totalWorkMinutes] = totalMins
            }

            buildAttendanceResponse(record[AttendanceTable.id])
        }
    }

    suspend fun getHistory(userId: Long, month: Int? = null, year: Int? = null): List<AttendanceResponse> {
        val targetYear = year ?: LocalDate.now().year
        val targetMonth = month ?: LocalDate.now().monthValue

        return DatabaseFactory.dbQuery {
            AttendanceTable
                .select {
                    AttendanceTable.userId eq userId
                }
                .orderBy(AttendanceTable.date to SortOrder.DESC)
                .limit(30)
                .map { mapAttendanceRow(it) }
        }
    }

    suspend fun getAttendanceCountersToday(): AttendanceSummaryCounters {
        val today = LocalDate.now()
        return DatabaseFactory.dbQuery {
            val totalPerangkat = PerangkatDesaTable.selectAll().count().toInt()
            val logs = AttendanceTable.select { AttendanceTable.date eq today }.toList()

            val hadir = logs.count { it[AttendanceTable.status] == "HADIR" }
            val terlambat = logs.count { it[AttendanceTable.status] == "TERLAMBAT" }
            val izin = logs.count { it[AttendanceTable.status] == "IZIN" }
            val sakit = logs.count { it[AttendanceTable.status] == "SAKIT" }
            val cuti = logs.count { it[AttendanceTable.status] == "CUTI" }
            val dinasLuar = logs.count { it[AttendanceTable.status] == "DINAS_LUAR" }
            val belumHadir = (totalPerangkat - (hadir + terlambat + izin + sakit + cuti + dinasLuar)).coerceAtLeast(0)

            AttendanceSummaryCounters(
                totalPerangkat = totalPerangkat,
                hadir = hadir,
                terlambat = terlambat,
                izin = izin,
                sakit = sakit,
                cuti = cuti,
                dinasLuar = dinasLuar,
                belumHadir = belumHadir
            )
        }
    }

    private fun buildAttendanceResponse(id: Long): AttendanceResponse {
        val row = AttendanceTable.select { AttendanceTable.id eq id }.single()
        return mapAttendanceRow(row)
    }

    private fun mapAttendanceRow(row: ResultRow): AttendanceResponse {
        val uId = row[AttendanceTable.userId]
        val userRow = UsersTable.select { UsersTable.id eq uId }.singleOrNull()
        val perangkatRow = PerangkatDesaTable.select { PerangkatDesaTable.userId eq uId }.singleOrNull()

        val totalMins = row[AttendanceTable.totalWorkMinutes]
        val workHoursStr = if (totalMins > 0) {
            val h = totalMins / 60
            val m = totalMins % 60
            "${h}h${m}m"
        } else "--"

        return AttendanceResponse(
            id = row[AttendanceTable.id],
            userId = uId,
            userName = userRow?.get(UsersTable.fullName) ?: "Aparatur Desa",
            userJabatan = perangkatRow?.get(PerangkatDesaTable.jabatan) ?: "Perangkat Desa",
            date = row[AttendanceTable.date].toString(),
            clockIn = row[AttendanceTable.clockIn]?.format(displayTimeFormatter),
            clockOut = row[AttendanceTable.clockOut]?.format(displayTimeFormatter),
            workHours = workHoursStr,
            breakTime = "1h",
            status = row[AttendanceTable.status],
            lateMinutes = row[AttendanceTable.lateMinutes],
            earlyLeaveMinutes = row[AttendanceTable.earlyLeaveMinutes],
            distanceMeters = row[AttendanceTable.clockInDistanceMeters]?.toDouble() ?: 0.0,
            isWithinOfficeRadius = true,
            faceConfidence = row[AttendanceTable.faceConfidenceIn]?.toDouble() ?: 98.5,
            livenessPassed = row[AttendanceTable.livenessPassedIn],
            verificationMethod = row[AttendanceTable.verificationMethod],
            notes = row[AttendanceTable.notes]
        )
    }
}
