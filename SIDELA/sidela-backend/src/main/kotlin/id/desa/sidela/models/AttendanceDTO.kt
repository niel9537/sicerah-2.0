package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class ClockInRequest(
    val latitude: Double,
    val longitude: Double,
    val faceEmbedding: List<Float>,
    val livenessScore: Float = 1.0f,
    val photoBase64: String? = null,
    val notes: String? = null
)

@Serializable
data class ClockOutRequest(
    val latitude: Double,
    val longitude: Double,
    val faceEmbedding: List<Float>,
    val livenessScore: Float = 1.0f,
    val photoBase64: String? = null,
    val notes: String? = null
)

@Serializable
data class AttendanceResponse(
    val id: Long,
    val userId: Long,
    val userName: String,
    val userJabatan: String,
    val date: String,
    val clockIn: String? = null,
    val clockOut: String? = null,
    val workHours: String? = null,
    val breakTime: String = "1h",
    val status: String,
    val lateMinutes: Int = 0,
    val earlyLeaveMinutes: Int = 0,
    val distanceMeters: Double = 0.0,
    val isWithinOfficeRadius: Boolean = true,
    val faceConfidence: Double = 0.0,
    val livenessPassed: Boolean = true,
    val verificationMethod: String = "FACE_RECOGNITION",
    val notes: String? = null
)

@Serializable
data class TodayAttendanceSummary(
    val date: String,
    val dayName: String,
    val clockIn: String? = "--",
    val clockOut: String? = "--",
    val workHours: String = "--",
    val breakTime: String = "--",
    val statusToday: String = "BELUM_HADIR",
    val isClockInAllowed: Boolean = true,
    val isClockOutAllowed: Boolean = false,
    val serverTime: String,
    val officeRadiusMeters: Int = 50,
    val officeLatitude: Double = 1.0425000,
    val officeLongitude: Double = 97.5258000
)

@Serializable
data class AttendanceSummaryCounters(
    val totalPerangkat: Int,
    val hadir: Int,
    val terlambat: Int,
    val izin: Int,
    val sakit: Int,
    val cuti: Int,
    val dinasLuar: Int,
    val belumHadir: Int
)

@Serializable
data class AttendanceSettingDTO(
    val id: Int = 1,
    val officeName: String,
    val latitude: Double,
    val longitude: Double,
    val radiusMeters: Int,
    val workStartTime: String,
    val workEndTime: String,
    val lateToleranceMinutes: Int,
    val breakStartTime: String,
    val breakEndTime: String,
    val isActive: Boolean = true
)

@Serializable
data class ManualAttendanceRequest(
    val date: String,
    val type: String, // CLOCK_IN or CLOCK_OUT
    val time: String,
    val reason: String,
    val proofAttachmentUrl: String? = null
)
