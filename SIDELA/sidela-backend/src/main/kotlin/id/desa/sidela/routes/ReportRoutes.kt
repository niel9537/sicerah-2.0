package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.ReportService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*
import java.time.LocalDate

fun Route.reportRoutes() {
    route("/api/v1/reports") {
        authenticate("auth-jwt") {
            get("/attendance") {
                val startDate = call.request.queryParameters["startDate"] ?: LocalDate.now().withDayOfMonth(1).toString()
                val endDate = call.request.queryParameters["endDate"] ?: LocalDate.now().toString()
                val userId = call.request.queryParameters["userId"]?.toLongOrNull()

                val filter = ReportFilterRequest(startDate, endDate, userId)
                val report = ReportService.generateAttendanceReport(filter)

                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Laporan presensi berhasil dibuat.", data = report)
                )
            }

            post("/attendance/export/csv") {
                val filter = call.receive<ReportFilterRequest>()
                val exportUrl = ReportService.exportAttendanceCsv(filter)

                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Ekspor CSV berhasil dibuat.", data = exportUrl)
                )
            }

            post("/attendance/export/pdf") {
                val filter = call.receive<ReportFilterRequest>()
                val exportUrl = ReportService.exportAttendancePdf(filter)

                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Ekspor PDF/Cetak berhasil dibuat.", data = exportUrl)
                )
            }
        }
    }
}
