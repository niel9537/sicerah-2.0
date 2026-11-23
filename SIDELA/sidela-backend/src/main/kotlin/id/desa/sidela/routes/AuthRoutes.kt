package id.desa.sidela.routes

import id.desa.sidela.models.*
import id.desa.sidela.services.AuthService
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.auth.jwt.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*

fun Route.authRoutes() {
    route("/api/v1/auth") {
        post("/login") {
            val req = call.receive<LoginRequest>()
            val authResult = AuthService.login(req)
            if (authResult != null) {
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(
                        success = true,
                        message = "Login berhasil. Selamat datang di SIDELA Iraonogela.",
                        data = authResult
                    )
                )
            } else {
                call.respond(
                    HttpStatusCode.Unauthorized,
                    ApiResponse<Unit>(
                        success = false,
                        message = "Username atau password salah, atau akun Anda dinonaktifkan."
                    )
                )
            }
        }

        authenticate("auth-jwt") {
            get("/me") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val profile = AuthService.getUserProfile(userId)
                if (profile != null) {
                    call.respond(
                        HttpStatusCode.OK,
                        ApiResponse(
                            success = true,
                            message = "Profil berhasil diambil.",
                            data = profile
                        )
                    )
                } else {
                    call.respond(
                        HttpStatusCode.NotFound,
                        ApiResponse<Unit>(success = false, message = "Profil tidak ditemukan.")
                    )
                }
            }

            post("/change-password") {
                val principal = call.principal<JWTPrincipal>()
                val userId = principal?.payload?.getClaim("userId")?.asLong() ?: 0L
                val req = call.receive<ChangePasswordRequest>()
                val success = AuthService.changePassword(userId, req)
                if (success) {
                    call.respond(
                        HttpStatusCode.OK,
                        ApiResponse(success = true, message = "Password berhasil diubah.", data = true)
                    )
                } else {
                    call.respond(
                        HttpStatusCode.BadRequest,
                        ApiResponse<Unit>(success = false, message = "Password lama salah.")
                    )
                }
            }

            get("/users") {
                val users = AuthService.listUsers()
                call.respond(
                    HttpStatusCode.OK,
                    ApiResponse(success = true, message = "Daftar pengguna berhasil diambil.", data = users)
                )
            }
        }
    }
}
