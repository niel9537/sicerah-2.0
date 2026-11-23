package id.desa.sidela.models

import kotlinx.serialization.Serializable

@Serializable
data class FaceEnrollRequest(
    val userId: Long? = null,
    val faceEmbedding: List<Float>,
    val facePhotoBase64: String,
    val modelVersion: String = "MobileFaceNet-V1-MLKit"
)

@Serializable
data class FaceVerifyRequest(
    val userId: Long,
    val faceEmbedding: List<Float>,
    val livenessScore: Float = 1.0f
)

@Serializable
data class FaceVerifyResult(
    val isMatched: Boolean,
    val confidencePercentage: Double,
    val cosineDistance: Double,
    val livenessPassed: Boolean,
    val message: String
)

@Serializable
data class FaceResetRequest(
    val targetUserId: Long,
    val reason: String
)

@Serializable
data class FaceProfileDTO(
    val id: Long,
    val userId: Long,
    val fullName: String,
    val isEnrolled: Boolean,
    val enrolledAt: String? = null,
    val lastVerifiedAt: String? = null,
    val resetCount: Int = 0,
    val faceImageUrl: String? = null
)
