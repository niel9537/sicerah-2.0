package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.JournalService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.journalRoutes() {
    route("/api/v1/journals") {
        authenticate("auth-jwt") {
            get {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: "perangkat_desa"
                val date = call.request.queryParameters["date"]
                val status = call.request.queryParameters["status"]

                val journals = JournalService.getJournals(userId, role, date, status)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Daftar jurnal kerja berhasil diambil.", data = journals)
                )
            }

            post {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val input = call.receive<JournalInput>()

                val created = JournalService.createJournal(userId, input)
                call.respond(
                    HttpStatusCode.Created,
                    ApiResponse(success = true, message = "Aktivitas jurnal berhasil dicatat.", data = created)
                )
            }

            put("/{id}") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val id = call.parameters["id"]?.toLongOrNull() ?: 0L
                val input = call.receive<JournalInput>()

                val updated = JournalService.updateJournal(id, userId, input)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Jurnal berhasil diperbarui.", data = updated)
                )
            }

            put("/{id}/verify") {
                val principal = call.principal<JWTPrincipal>()
                val reviewerId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: ""

                if (role != "admin" && role != "kepala_desa" && role != "sekretaris_desa") {
                    call.respond(
                        HttpStatusCode.Forbidden,
                        ApiResponse<Unit>(success = false, message = "Hanya Kepala Desa atau Sekretaris Desa yang berhak memverifikasi jurnal.")
                    )
                    return@put
                }

                val id = call.parameters["id"]?.toLongOrNull() ?: 0L
                val input = call.receive<JournalVerifyInput>()

                val verified = JournalService.verifyJournal(id, reviewerId, input)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Jurnal kerja berhasil diverifikasi.", data = verified)
                )
            }
        }
    }
}
