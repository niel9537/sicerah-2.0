package id.desa.sidela.data.model

import com.google.gson.annotations.SerializedName

data class ApiResponse<T>(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String,
    @SerializedName("data") val data: T?,
    @SerializedName("errors") val errors: List<String>?,
    @SerializedName("timestamp") val timestamp: Long
)

data class LoginRequest(
    @SerializedName("username") val username: String,
    @SerializedName("password") val password: String
)

data class AuthResponse(
    @SerializedName("token") val token: String,
    @SerializedName("tokenType") val tokenType: String,
    @SerializedName("expiresInSeconds") val expiresInSeconds: Long,
    @SerializedName("user") val user: UserProfile
)

data class UserProfile(
    @SerializedName("id") val id: Long,
    @SerializedName("username") val username: String,
    @SerializedName("email") val email: String,
    @SerializedName("fullName") val fullName: String,
    @SerializedName("nik") val nik: String,
    @SerializedName("phone") val phone: String?,
    @SerializedName("avatarUrl") val avatarUrl: String?,
    @SerializedName("role") val role: Role,
    @SerializedName("perangkatDesa") val perangkatDesa: PerangkatDesa?,
    @SerializedName("isFaceEnrolled") val isFaceEnrolled: Boolean
)

data class Role(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("description") val description: String?
)

data class PerangkatDesa(
    @SerializedName("id") val id: Long,
    @SerializedName("nipd") val nipd: String?,
    @SerializedName("jabatan") val jabatan: String,
    @SerializedName("dusunId") val dusunId: Int?,
    @SerializedName("dusunName") val dusunName: String?,
    @SerializedName("alamat") val alamat: String?,
    @SerializedName("tanggalSk") val tanggalSk: String?,
    @SerializedName("nomorSk") val nomorSk: String?,
    @SerializedName("statusKeaktifan") val statusKeaktifan: String
)

data class TodayAttendanceSummary(
    @SerializedName("date") val date: String,
    @SerializedName("dayName") val dayName: String,
    @SerializedName("clockIn") val clockIn: String?,
    @SerializedName("clockOut") val clockOut: String?,
    @SerializedName("workHours") val workHours: String,
    @SerializedName("breakTime") val breakTime: String,
    @SerializedName("statusToday") val statusToday: String,
    @SerializedName("isClockInAllowed") val isClockInAllowed: Boolean,
    @SerializedName("isClockOutAllowed") val isClockOutAllowed: Boolean,
    @SerializedName("serverTime") val serverTime: String,
    @SerializedName("officeRadiusMeters") val officeRadiusMeters: Int,
    @SerializedName("officeLatitude") val officeLatitude: Double,
    @SerializedName("officeLongitude") val officeLongitude: Double
)

data class AttendanceRecord(
    @SerializedName("id") val id: Long,
    @SerializedName("userId") val userId: Long,
    @SerializedName("userName") val userName: String,
    @SerializedName("userJabatan") val userJabatan: String,
    @SerializedName("date") val date: String,
    @SerializedName("clockIn") val clockIn: String?,
    @SerializedName("clockOut") val clockOut: String?,
    @SerializedName("workHours") val workHours: String?,
    @SerializedName("breakTime") val breakTime: String,
    @SerializedName("status") val status: String,
    @SerializedName("lateMinutes") val lateMinutes: Int,
    @SerializedName("earlyLeaveMinutes") val earlyLeaveMinutes: Int,
    @SerializedName("distanceMeters") val distanceMeters: Double,
    @SerializedName("isWithinOfficeRadius") val isWithinOfficeRadius: Boolean,
    @SerializedName("faceConfidence") val faceConfidence: Double,
    @SerializedName("livenessPassed") val livenessPassed: Boolean,
    @SerializedName("verificationMethod") val verificationMethod: String,
    @SerializedName("notes") val notes: String?
)

data class ClockInRequest(
    @SerializedName("latitude") val latitude: Double,
    @SerializedName("longitude") val longitude: Double,
    @SerializedName("faceEmbedding") val faceEmbedding: List<Float>,
    @SerializedName("livenessScore") val livenessScore: Float,
    @SerializedName("photoBase64") val photoBase64: String?,
    @SerializedName("notes") val notes: String?
)

data class ClockOutRequest(
    @SerializedName("latitude") val latitude: Double,
    @SerializedName("longitude") val longitude: Double,
    @SerializedName("faceEmbedding") val faceEmbedding: List<Float>,
    @SerializedName("livenessScore") val livenessScore: Float,
    @SerializedName("photoBase64") val photoBase64: String?,
    @SerializedName("notes") val notes: String?
)

data class RequestStatusCounters(
    @SerializedName("totalRequests") val totalRequests: Int,
    @SerializedName("approved") val approved: Int,
    @SerializedName("rejected") val rejected: Int,
    @SerializedName("pending") val pending: Int
)

data class AttendanceSummaryCounters(
    @SerializedName("totalPerangkat") val totalPerangkat: Int,
    @SerializedName("hadir") val hadir: Int,
    @SerializedName("terlambat") val terlambat: Int,
    @SerializedName("izin") val izin: Int,
    @SerializedName("sakit") val sakit: Int,
    @SerializedName("cuti") val cuti: Int,
    @SerializedName("dinasLuar") val dinasLuar: Int,
    @SerializedName("belumHadir") val belumHadir: Int
)

data class WorkJournalItem(
    @SerializedName("id") val id: Long,
    @SerializedName("userId") val userId: Long,
    @SerializedName("userName") val userName: String,
    @SerializedName("userJabatan") val userJabatan: String,
    @SerializedName("date") val date: String,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String,
    @SerializedName("startTime") val startTime: String,
    @SerializedName("endTime") val endTime: String,
    @SerializedName("workOutput") val workOutput: String,
    @SerializedName("status") val status: String,
    @SerializedName("feedbackNotes") val feedbackNotes: String?,
    @SerializedName("verifiedBy") val verifiedBy: String?,
    @SerializedName("verifiedAt") val verifiedAt: String?,
    @SerializedName("createdAt") val createdAt: String
)

data class LeaveItem(
    @SerializedName("id") val id: Long,
    @SerializedName("userId") val userId: Long,
    @SerializedName("userName") val userName: String,
    @SerializedName("userJabatan") val userJabatan: String,
    @SerializedName("leaveType") val leaveType: String,
    @SerializedName("startDate") val startDate: String,
    @SerializedName("endDate") val endDate: String,
    @SerializedName("totalDays") val totalDays: Int,
    @SerializedName("reason") val reason: String,
    @SerializedName("attachmentUrl") val attachmentUrl: String?,
    @SerializedName("destinationLocation") val destinationLocation: String?,
    @SerializedName("officialDutyNumber") val officialDutyNumber: String?,
    @SerializedName("status") val status: String,
    @SerializedName("approvedBy") val approvedBy: String?,
    @SerializedName("approvedAt") val approvedAt: String?,
    @SerializedName("rejectionNotes") val rejectionNotes: String?,
    @SerializedName("createdAt") val createdAt: String
)

data class DashboardSummary(
    @SerializedName("user") val user: UserProfile,
    @SerializedName("todaySummary") val todaySummary: TodayAttendanceSummary,
    @SerializedName("requestStatus") val requestStatus: RequestStatusCounters,
    @SerializedName("attendanceSummary") val attendanceSummary: AttendanceSummaryCounters,
    @SerializedName("recentTasks") val recentTasks: List<WorkJournalItem>,
    @SerializedName("recentRequests") val recentRequests: List<LeaveItem>
)

data class FaceVerifyResult(
    @SerializedName("isMatched") val isMatched: Boolean,
    @SerializedName("confidencePercentage") val confidencePercentage: Double,
    @SerializedName("cosineDistance") val cosineDistance: Double,
    @SerializedName("livenessPassed") val livenessPassed: Boolean,
    @SerializedName("message") val message: String
)
