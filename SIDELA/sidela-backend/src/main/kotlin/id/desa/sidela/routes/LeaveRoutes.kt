package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.LeaveService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.leaveRoutes() {
    route("/api/v1/leaves") {
        authenticate("auth-jwt") {
            get {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: "perangkat_desa"
                val status = call.request.queryParameters["status"]

                val leaves = LeaveService.getLeaves(userId, role, status)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Daftar pengajuan berhasil diambil.", data = leaves)
                )
            }

            post {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<LeaveRequestInput>()

                val created = LeaveService.submitLeave(userId, req)
                call.respond(
                    HttpStatusCode.Created,
                    ApiResponse(success = true, message = "Pengajuan berhasil dikirimkan.", data = created)
                )
            }

            get("/counters") {
                val counters = LeaveService.getRequestStatusCounters()
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Status pengajuan berhasil dihitung.", data = counters)
                )
            }

            put("/{id}/approve") {
                val principal = call.principal<JWTPrincipal>()
                val reviewerId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: ""

                if (role != "admin" && role != "kepala_desa" && role != "sekretaris_desa") {
                    call.respond(
                        HttpStatusCode.Forbidden,
                        ApiResponse<Unit>(success = false, message = "Hanya Kepala Desa atau pejabat berwenang yang dapat menyetujui pengajuan.")
                    )
                    return@put
                }

                val leaveId = call.parameters["id"]?.toLongOrNull() ?: 0L
                val body = try { call.receive<ApprovalActionInput>() } catch (e: Exception) { ApprovalActionInput("APPROVE") }

                val approved = LeaveService.approveLeave(leaveId, reviewerId, body.notes)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Pengajuan berhasil disetujui.", data = approved)
                )
            }

            put("/{id}/reject") {
                val principal = call.principal<JWTPrincipal>()
                val reviewerId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: ""

                if (role != "admin" && role != "kepala_desa" && role != "sekretaris_desa") {
                    call.respond(
                        HttpStatusCode.Forbidden,
                        ApiResponse<Unit>(success = false, message = "Hanya Kepala Desa atau pejabat berwenang yang dapat menolak pengajuan.")
                    )
                    return@put
                }

                val leaveId = call.parameters["id"]?.toLongOrNull() ?: 0L
                val body = call.receive<ApprovalActionInput>()

                val rejected = LeaveService.rejectLeave(leaveId, reviewerId, body.notes)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Pengajuan berhasil ditolak.", data = rejected)
                )
            }
        }
    }
}
