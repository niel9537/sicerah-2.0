package id.desa.sidela.data.remote

import id.desa.sidela.data.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // Auth
    @POST("api/v1/auth/login")
    suspend fun login(@Body request: LoginRequest): Response<ApiResponse<AuthResponse>>

    @GET("api/v1/auth/me")
    suspend fun getProfile(): Response<ApiResponse<UserProfile>>

    // Dashboard
    @GET("api/v1/dashboard/summary")
    suspend fun getDashboardSummary(): Response<ApiResponse<DashboardSummary>>

    // Attendance
    @GET("api/v1/attendance/today")
    suspend fun getTodayAttendance(): Response<ApiResponse<TodayAttendanceSummary>>

    @POST("api/v1/attendance/clock-in")
    suspend fun clockIn(@Body request: ClockInRequest): Response<ApiResponse<AttendanceRecord>>

    @POST("api/v1/attendance/clock-out")
    suspend fun clockOut(@Body request: ClockOutRequest): Response<ApiResponse<AttendanceRecord>>

    @GET("api/v1/attendance/history")
    suspend fun getAttendanceHistory(
        @Query("month") month: Int? = null,
        @Query("year") year: Int? = null
    ): Response<ApiResponse<List<AttendanceRecord>>>

    // Leaves
    @GET("api/v1/leaves")
    suspend fun getLeaves(@Query("status") status: String? = null): Response<ApiResponse<List<LeaveItem>>>

    @GET("api/v1/leaves/counters")
    suspend fun getLeaveCounters(): Response<ApiResponse<RequestStatusCounters>>

    // Journals
    @GET("api/v1/journals")
    suspend fun getJournals(
        @Query("date") date: String? = null,
        @Query("status") status: String? = null
    ): Response<ApiResponse<List<WorkJournalItem>>>

    @POST("api/v1/journals")
    suspend fun createJournal(@Body item: WorkJournalItem): Response<ApiResponse<WorkJournalItem>>
}
