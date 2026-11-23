package id.desa.sidela.plugins

import id.desa.sidela.routes.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Application.configureRouting() {
    routing {
        get("/") {
            call.respondText("SIDELA Backend API - Pemerintah Desa Iraonogela (Online & Ready)")
        }

        get("/api/v1/health") {
            call.respond(mapOf("status" to "UP", "service" to "SIDELA Backend", "version" to "1.0.0"))
        }

        authRoutes()
        attendanceRoutes()
        faceRoutes()
        leaveRoutes()
        journalRoutes()
        villageRoutes()
        dashboardRoutes()
        reportRoutes()
        uploadRoutes()
    }
}
