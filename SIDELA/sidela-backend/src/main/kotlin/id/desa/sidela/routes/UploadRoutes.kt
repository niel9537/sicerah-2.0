package id.desa.sidela.routes

import id.desa.sidela.models.ApiResponse
import id.desa.sidela.utils.FileUploadHelper
import io.ktor.http.*
import io.ktor.http.content.*
import io.ktor.server.application.*
import io.ktor.server.auth.*
import io.ktor.server.http.content.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import io.ktor.server.routing.*
import java.io.File
import java.util.UUID

fun Route.uploadRoutes() {
    // Serve static files from ./uploads at /uploads
    staticFiles("/uploads", FileUploadHelper.getUploadsDirectory())

    route("/api/v1/upload") {
        authenticate("auth-jwt") {
            post {
                val multipartData = call.receiveMultipart()
                var fileUrl = ""

                multipartData.forEachPart { part ->
                    if (part is PartData.FileItem) {
                        val originalName = part.originalFileName ?: "file"
                        val ext = originalName.substringAfterLast(".", "bin")
                        val fileName = "doc_${UUID.randomUUID()}.$ext"
                        val file = File(FileUploadHelper.getUploadsDirectory(), fileName)
                        part.streamProvider().use { input ->
                            file.outputStream().buffered().use { output ->
                                input.copyTo(output)
                            }
                        }
                        fileUrl = "/uploads/$fileName"
                    }
                    part.dispose()
                }

                if (fileUrl.isNotBlank()) {
                    call.respond(
                        HttpStatusCode.OK,
                        ApiResponse(
                            success = true,
                            message = "File berhasil diunggah.",
                            data = fileUrl
                        )
                    )
                } else {
                    call.respond(
                        HttpStatusCode.BadRequest,
                        ApiResponse<Unit>(success = false, message = "Tidak ada file yang diunggah.")
                    )
                }
            }
        }
    }
}
