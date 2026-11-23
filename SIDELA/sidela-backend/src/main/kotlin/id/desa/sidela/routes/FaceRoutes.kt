package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.FaceRecognitionService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.faceRoutes() {
    route("/api/v1/face") {
        authenticate("auth-jwt") {
            post("/enroll") {
                val principal = call.principal<JWTPrincipal>()
                val currentUserId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<FaceEnrollRequest>()
                val targetUserId = req.userId ?: currentUserId

                val success = FaceRecognitionService.enrollFace(req, targetUserId)
                if (success) {
                    call.respond(
                        HttpStatusCode.OK,
                        ApiResponse(success = true, message = "Perekaman biometrik wajah berhasil disimpan.", data = true)
                    )
                } else {
                    call.respond(
                        HttpStatusCode.InternalServerError,
                        ApiResponse<Unit>(success = false, message = "Gagal menyimpan biometrik wajah.")
                    )
                }
            }

            post("/verify") {
                val principal = call.principal<JWTPrincipal>()
                val currentUserId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<FaceVerifyRequest>()
                val targetUserId = if (req.userId > 0) req.userId else currentUserId

                val result = FaceRecognitionService.verifyFace(targetUserId, req.faceEmbedding, req.livenessScore)
                call.respond(
                    if (result.isMatched) HttpStatusCode.OK else HttpStatusCode.BadRequest,
                    ApiResponse(success = result.isMatched, message = result.message, data = result)
                )
            }

            post("/reset") {
                val principal = call.principal<JWTPrincipal>()
                val adminUserId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val role = principal?.payload?.getClaim("role")?.asString() ?: ""

                if (role != "admin" && role != "kepala_desa") {
                    call.respond(
                        HttpStatusCode.Forbidden,
                        ApiResponse<Unit>(success = false, message = "Hanya Administrator atau Kepala Desa yang dapat mereset data biometrik wajah.")
                    )
                    return@post
                }

                val req = call.receive<FaceResetRequest>()
                val success = FaceRecognitionService.resetFace(req.targetUserId, adminUserId, req.reason)
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = success, message = "Data biometrik wajah pengguna berhasil direset.", data = success)
                )
            }
        }
    }
}
