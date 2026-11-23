package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class DashboardSummaryResponse(
    val user: UserProfileDTO,
    val todaySummary: TodayAttendanceSummary,
    val requestStatus: RequestStatusCounters,
    val attendanceSummary: AttendanceSummaryCounters,
    val recentTasks: List<JournalResponse>,
    val recentRequests: List<LeaveResponse>,
    val attendanceChartWeekly: List<WeeklyChartPoint> = emptyList()
)

@Serializable
data class WeeklyChartPoint(
    val day: String,
    val date: String,
    val hadirCount: Int,
    val terlambatCount: Int,
    val izinCount: Int
)

@Serializable
data class NotificationDTO(
    val id: Long,
    val title: String,
    val message: String,
    val type: String,
    val isRead: Boolean,
    val createdAt: String
)
