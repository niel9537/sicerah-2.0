package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.AttendanceService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.attendanceRoutes() {
    route("/api/v1/attendance") {
        authenticate("auth-jwt") {
            get("/today") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val summary = AttendanceService.getTodaySummary(userId)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Status presensi hari ini berhasil diambil.", data = summary)
                )
            }

            post("/clock-in") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<ClockInRequest>()
                val result = AttendanceService.clockIn(userId, req)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Presensi masuk berhasil dicatat.", data = result)
                )
            }

            post("/clock-out") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<ClockOutRequest>()
                val result = AttendanceService.clockOut(userId, req)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Presensi pulang berhasil dicatat.", data = result)
                )
            }

            get("/history") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val month = call.request.queryParameters["month"]?.toIntOrNull()
                val year = call.request.queryParameters["year"]?.toIntOrNull()
                val history = AttendanceService.getHistory(userId, month, year)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Riwayat presensi berhasil diambil.", data = history)
                )
            }

            get("/summary") {
                val counters = AttendanceService.getAttendanceCountersToday()
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Ringkasan presensi kantor hari ini.", data = counters)
                )
            }

            get("/settings") {
                val settings = AttendanceService.getSettings()
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Pengaturan presensi berhasil diambil.", data = settings)
                )
            }
        }
    }
}
