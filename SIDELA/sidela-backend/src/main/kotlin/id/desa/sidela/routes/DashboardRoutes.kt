package id.desa.sidela.routes

import id.desa.sidela.models.ApiResponse
import id.desa.sidela.services.DashboardService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.dashboardRoutes() {
    route("/api/v1/dashboard") {
        authenticate("auth-jwt") {
            get("/summary") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val summary = DashboardService.getDashboardSummary(userId)

                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(
                        success = true,
                        message = "Data dashboard SIDELA berhasil dimuat.",
                        data = summary
                    )
                )
            }
        }
    }
}
