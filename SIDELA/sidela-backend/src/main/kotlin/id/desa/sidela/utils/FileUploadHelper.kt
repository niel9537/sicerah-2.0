package id.desa.sidela.utils

import java.io.File
import java.util.Base64
import java.util.UUID

object FileUploadHelper {
    private val uploadDir = File("./uploads").apply {
        if (!exists()) mkdirs()
    }

    fun saveBase64Image(base64Data: String, prefix: String = "face"): String {
        return try {
            val cleanBase64 = if (base64Data.contains(",")) {
                base64Data.substringAfter(",")
            } else {
                base64Data
            }
            val decodedBytes = Base64.getDecoder().decode(cleanBase64)
            val fileName = "${prefix}_${UUID.randomUUID()}.jpg"
            val targetFile = File(uploadDir, fileName)
            targetFile.writeBytes(decodedBytes)
            "/uploads/$fileName"
        } catch (e: Exception) {
            "/uploads/default_avatar.jpg"
        }
    }

    fun getUploadsDirectory(): File = uploadDir
}
