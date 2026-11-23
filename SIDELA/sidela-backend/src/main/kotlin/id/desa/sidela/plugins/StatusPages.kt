package id.desa.sidela.plugins

import id.desa.sidela.models.ApiResponse
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.plugins.statuspages.*
import io.ktor.server.response.*

fun Application.configureStatusPages() {
    install(StatusPages) {
        exception<IllegalArgumentException> { call, cause ->
            call.respond(
                HttpStatusCode.BadRequest,
                ApiResponse<Unit>(
                    success = false,
                    message = cause.message ?: "Permintaan tidak valid.",
                    errors = listOf(cause.localizedMessage)
                )
            )
        }
        exception<IllegalStateException> { call, cause ->
            call.respond(
                HttpStatusCode.Conflict,
                ApiResponse<Unit>(
                    success = false,
                    message = cause.message ?: "Konflik status data.",
                    errors = listOf(cause.localizedMessage)
                )
            )
        }
        exception<Throwable> { call, cause ->
            cause.printStackTrace()
            call.respond(
                HttpStatusCode.InternalServerError,
                ApiResponse<Unit>(
                    success = false,
                    message = "Terjadi kesalahan internal pada server: ${cause.message}",
                    errors = listOf(cause.localizedMessage)
                )
            )
        }
        status(HttpStatusCode.Unauthorized) { call, status ->
            call.respond(
                status,
                ApiResponse<Unit>(
                    success = false,
                    message = "Akses ditolak. Sesi login telah berakhir atau token tidak valid."
                )
            )
        }
    }
}
