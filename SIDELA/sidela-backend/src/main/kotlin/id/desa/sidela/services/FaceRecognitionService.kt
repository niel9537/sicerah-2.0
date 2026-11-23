package id.desa.sidela.services

import id.desa.sidela.database.*
import id.desa.sidela.models.*
import id.desa.sidela.utils.FileUploadHelper
import kotlinx.serialization.json.Json
import org.jetbrains.exposed.sql.*
import java.time.LocalDateTime
import kotlin.math.sqrt

object FaceRecognitionService {
    private val json = Json { ignoreUnknownKeys = true }

    /**
     * Menghitung kemiripan Cosine Similarity antara dua vektor embedding wajah (192 / 512 dimensi).
     * Nilai berkisar antara -1.0 hingga +1.0 (nilai > 0.75 dianggap orang yang sama).
     */
    fun calculateCosineSimilarity(vecA: List<Float>, vecB: List<Float>): Double {
        if (vecA.isEmpty() || vecB.isEmpty() || vecA.size != vecB.size) {
            return 0.0
        }

        var dotProduct = 0.0
        var normA = 0.0
        var normB = 0.0

        for (i in vecA.indices) {
            val a = vecA[i].toDouble()
            val b = vecB[i].toDouble()
            dotProduct += a * b
            normA += a * a
            normB += b * b
        }

        if (normA == 0.0 || normB == 0.0) return 0.0

        return dotProduct / (sqrt(normA) * sqrt(normB))
    }

    suspend fun enrollFace(req: FaceEnrollRequest, targetUserId: Long): Boolean {
        return DatabaseFactory.dbQuery {
            val photoUrl = FileUploadHelper.saveBase64Image(req.facePhotoBase64, "face_enrolled_${targetUserId}")
            val embeddingJson = json.encodeToString(kotlinx.serialization.builtins.ListSerializer(kotlinx.serialization.serializer()), req.faceEmbedding)

            val existing = FaceProfilesTable.select { FaceProfilesTable.userId eq targetUserId }.singleOrNull()

            if (existing != null) {
                FaceProfilesTable.update({ FaceProfilesTable.userId eq targetUserId }) {
                    it[faceEmbedding] = embeddingJson
                    it[faceImageUrl] = photoUrl
                    it[isEnrolled] = true
                    it[enrolledAt] = LocalDateTime.now()
                    it[modelVersion] = req.modelVersion
                    it[isActive] = true
                } > 0
            } else {
                FaceProfilesTable.insert {
                    it[userId] = targetUserId
                    it[faceEmbedding] = embeddingJson
                    it[faceImageUrl] = photoUrl
                    it[isEnrolled] = true
                    it[enrolledAt] = LocalDateTime.now()
                    it[modelVersion] = req.modelVersion
                    it[isActive] = true
                }.insertedCount > 0
            }
        }
    }

    suspend fun verifyFace(userId: Long, liveEmbedding: List<Float>, livenessScore: Float): FaceVerifyResult {
        return DatabaseFactory.dbQuery {
            val profile = FaceProfilesTable.select {
                (FaceProfilesTable.userId eq userId) and (FaceProfilesTable.isActive eq true)
            }.singleOrNull()

            if (profile == null || !profile[FaceProfilesTable.isEnrolled]) {
                return@dbQuery FaceVerifyResult(
                    isMatched = false,
                    confidencePercentage = 0.0,
                    cosineDistance = 1.0,
                    livenessPassed = false,
                    message = "Data biometrik wajah belum terdaftar. Silakan hubungi Admin Desa untuk perekaman wajah."
                )
            }

            // Anti-spoofing liveness check threshold
            val isLivenessPassed = livenessScore >= 0.65f
            if (!isLivenessPassed) {
                return@dbQuery FaceVerifyResult(
                    isMatched = false,
                    confidencePercentage = 0.0,
                    cosineDistance = 1.0,
                    livenessPassed = false,
                    message = "Gagal verifikasi keaslian wajah (Liveness Detection). Pastikan Anda berkedip dan berada di depan kamera langsung."
                )
            }

            val storedEmbeddingJson = profile[FaceProfilesTable.faceEmbedding]
            val storedEmbedding = try {
                json.decodeFromString<List<Float>>(storedEmbeddingJson)
            } catch (e: Exception) {
                emptyList()
            }

            val similarity = calculateCosineSimilarity(liveEmbedding, storedEmbedding)
            val confidencePercent = (similarity * 100.0).coerceIn(0.0, 100.0)
            val isMatch = similarity >= 0.72 // Threshold akurasi standar mobile biometric

            if (isMatch) {
                FaceProfilesTable.update({ FaceProfilesTable.userId eq userId }) {
                    it[lastVerifiedAt] = LocalDateTime.now()
                }
            }

            FaceVerifyResult(
                isMatched = isMatch,
                confidencePercentage = confidencePercent,
                cosineDistance = 1.0 - similarity,
                livenessPassed = true,
                message = if (isMatch) "Wajah berhasil diverifikasi (${"%.1f".format(confidencePercent)}% cocok)"
                          else "Wajah tidak cocok dengan profil terdaftar (${"%.1f".format(confidencePercent)}% cocok). Coba lagi dengan pencahayaan cukup."
            )
        }
    }

    suspend fun resetFace(targetUserId: Long, adminUserId: Long, reason: String): Boolean {
        return DatabaseFactory.dbQuery {
            val updated = FaceProfilesTable.update({ FaceProfilesTable.userId eq targetUserId }) {
                it[isEnrolled] = false
                it[isActive] = false
                it[resetCount] = resetCount + 1
            }

            // Log activity
            AuditLogsTable.insert {
                it[userId] = adminUserId
                it[action] = "RESET_FACE_BIOMETRIC"
                it[ipAddress] = "127.0.0.1"
                it[details] = "{\"targetUserId\":$targetUserId, \"reason\":\"$reason\"}"
                it[createdAt] = LocalDateTime.now()
            }

            updated > 0
        }
    }
}
