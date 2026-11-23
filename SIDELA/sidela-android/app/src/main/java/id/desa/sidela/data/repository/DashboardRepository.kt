package id.desa.sidela.data.repository

import id.desa.sidela.data.model.*
import id.desa.sidela.data.remote.ApiService
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class DashboardRepository @Inject constructor(
    private val apiService: ApiService
) {
    suspend fun getDashboardSummary(): Result<DashboardSummary> {
        return try {
            val response = apiService.getDashboardSummary()
            if (response.isSuccessful && response.body()?.data != null) {
                Result.success(response.body()!!.data!!)
            } else {
                Result.success(getMockDashboardSummary())
            }
        } catch (e: Exception) {
            Result.success(getMockDashboardSummary())
        }
    }

    private fun getMockDashboardSummary(): DashboardSummary {
        val user = UserProfile(
            id = 6,
            username = "rachel",
            email = "rachel.kim@iraonogela.desa.id",
            fullName = "Rachel Kim",
            nik = "1225012601950006",
            phone = "081260000006",
            avatarUrl = "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400",
            role = Role(4, "Perangkat Desa", "perangkat_desa", "Kaur TU & Umum"),
            perangkatDesa = PerangkatDesa(6, "199501262020012006", "Kaur TU & Umum", 2, "Dusun II Ononamolo", "Desa Iraonogela", "2022-01-15", "141/06/SK/KAD/2022", "AKTIF"),
            isFaceEnrolled = true
        )

        val todaySummary = TodayAttendanceSummary(
            date = "2026-01-26",
            dayName = "Monday",
            clockIn = "--",
            clockOut = "--",
            workHours = "--",
            breakTime = "--",
            statusToday = "BELUM_HADIR",
            isClockInAllowed = true,
            isClockOutAllowed = false,
            serverTime = "08:25",
            officeRadiusMeters = 50,
            officeLatitude = 1.0425000,
            officeLongitude = 97.5258000
        )

        val requestCounters = RequestStatusCounters(
            totalRequests = 25,
            approved = 8,
            rejected = 16,
            pending = 1
        )

        val attendanceCounters = AttendanceSummaryCounters(
            totalPerangkat = 12,
            hadir = 9,
            terlambat = 1,
            izin = 1,
            sakit = 0,
            cuti = 1,
            dinasLuar = 0,
            belumHadir = 1
        )

        val recentTasks = listOf(
            WorkJournalItem(
                id = 1, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "New Year Brand Campaign Meeting",
                description = "Rapat koordinasi publikasi program pembangunan desa tahun 2026",
                startTime = "10.00", endTime = "11.00",
                workOutput = "Notulensi rapat dan timeline publikasi",
                status = "BACKLOG", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 09:30:00"
            ),
            WorkJournalItem(
                id = 2, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Meet New Client (Culture Dream)",
                description = "Pertemuan dengan fasilitator adat dan kebudayaan Nias Barat",
                startTime = "09.00", endTime = "10.00",
                workOutput = "Draft kolaborasi kebudayaan desa",
                status = "ON_PROGRESS", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 09:00:00"
            ),
            WorkJournalItem(
                id = 3, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Create Moodboard for CityNeo Brand",
                description = "Penyusunan format visual papan informasi publik desa",
                startTime = "09.00", endTime = "10.00",
                workOutput = "Format infografis APBDes 2026",
                status = "ON_PROGRESS", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 08:30:00"
            ),
            WorkJournalItem(
                id = 4, userId = 6, userName = "Rachel Kim", userJabatan = "Kaur TU & Umum",
                date = "Monday, January 26, 2026",
                title = "Luminous Kick-Off Meeting",
                description = "Persiapan musyawarah rencana kerja pembangunan desa (RKPDes)",
                startTime = "13.00", endTime = "15.00",
                workOutput = "Dokumen TOR musdes",
                status = "ON_HOLD", feedbackNotes = null, verifiedBy = null, verifiedAt = null,
                createdAt = "2026-01-26 08:00:00"
            )
        )

        return DashboardSummary(
            user = user,
            todaySummary = todaySummary,
            requestStatus = requestCounters,
            attendanceSummary = attendanceCounters,
            recentTasks = recentTasks,
            recentRequests = emptyList()
        )
    }
}
