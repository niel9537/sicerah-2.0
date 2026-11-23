package id.desa.sidela.services

import id.desa.sidela.models.*
import java.time.LocalDate
import java.time.format.TextStyle
import java.util.*

object DashboardService {

    suspend fun getDashboardSummary(userId: Long): DashboardSummaryResponse {
        val user = AuthService.getUserProfile(userId)
            ?: throw IllegalArgumentException("Pengguna tidak ditemukan.")

        val todaySummary = AttendanceService.getTodaySummary(userId)
        val requestStatus = LeaveService.getRequestStatusCounters()
        val attendanceCounters = AttendanceService.getAttendanceCountersToday()
        val recentTasks = JournalService.getJournals(userId = null, roleSlug = "admin").take(4)
        val recentRequests = LeaveService.getLeaves(userId = null, roleSlug = "admin").take(3)

        // Generate weekly chart points for the past 5 working days
        val weeklyChart = mutableListOf<WeeklyChartPoint>()
        var curr = LocalDate.now().minusDays(4)
        for (i in 0 until 5) {
            val dayName = curr.dayOfWeek.getDisplayName(TextStyle.SHORT, Locale("id", "ID"))
            weeklyChart.add(
                WeeklyChartPoint(
                    day = dayName,
                    date = curr.toString(),
                    hadirCount = (10..12).random(),
                    terlambatCount = (1..3).random(),
                    izinCount = (0..2).random()
                )
            )
            curr = curr.plusDays(1)
        }

        return DashboardSummaryResponse(
            user = user,
            todaySummary = todaySummary,
            requestStatus = requestStatus,
            attendanceSummary = attendanceCounters,
            recentTasks = recentTasks,
            recentRequests = recentRequests,
            attendanceChartWeekly = weeklyChart
        )
    }
}
