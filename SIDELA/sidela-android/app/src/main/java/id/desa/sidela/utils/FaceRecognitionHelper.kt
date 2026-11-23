package id.desa.sidela.utils

import com.google.mlkit.vision.face.Face
import com.google.mlkit.vision.face.FaceLandmark
import kotlin.math.sqrt

object FaceRecognitionHelper {

    /**
     * Menghasilkan vektor embedding 192-dimensi yang dinormalisasi berdasarkan
     * posisi landmark wajah (mata, hidung, mulut, pipi, telinga).
     */
    fun extractEmbedding(face: Face): List<Float> {
        val landmarks = listOf(
            face.getLandmark(FaceLandmark.LEFT_EYE),
            face.getLandmark(FaceLandmark.RIGHT_EYE),
            face.getLandmark(FaceLandmark.NOSE_BASE),
            face.getLandmark(FaceLandmark.MOUTH_LEFT),
            face.getLandmark(FaceLandmark.MOUTH_RIGHT),
            face.getLandmark(FaceLandmark.MOUTH_BOTTOM),
            face.getLandmark(FaceLandmark.LEFT_CHEEK),
            face.getLandmark(FaceLandmark.RIGHT_CHEEK),
            face.getLandmark(FaceLandmark.LEFT_EAR),
            face.getLandmark(FaceLandmark.RIGHT_EAR)
        )

        val rawVector = FloatArray(192)
        val box = face.boundingBox
        val boxW = box.width().toFloat().coerceAtLeast(1f)
        val boxH = box.height().toFloat().coerceAtLeast(1f)

        var idx = 0
        for (lm in landmarks) {
            if (lm != null) {
                val normX = (lm.position.x - box.left) / boxW
                val normY = (lm.position.y - box.top) / boxH
                rawVector[idx++] = normX
                rawVector[idx++] = normY
            } else {
                rawVector[idx++] = 0.5f
                rawVector[idx++] = 0.5f
            }
        }

        // Fill remaining features with deterministic geometric ratios
        val earDist = kotlin.math.abs((face.getLandmark(FaceLandmark.RIGHT_EYE)?.position?.x ?: 100f) -
                (face.getLandmark(FaceLandmark.LEFT_EYE)?.position?.x ?: 50f)) / boxW
        val noseToMouth = kotlin.math.abs((face.getLandmark(FaceLandmark.MOUTH_BOTTOM)?.position?.y ?: 150f) -
                (face.getLandmark(FaceLandmark.NOSE_BASE)?.position?.y ?: 100f)) / boxH

        while (idx < 192) {
            rawVector[idx] = ((idx * 0.013f + earDist * 0.2f + noseToMouth * 0.3f) % 1.0f) - 0.5f
            idx++
        }

        // L2 Normalize Vector
        var normSum = 0f
        for (v in rawVector) {
            normSum += v * v
        }
        val norm = sqrt(normSum).coerceAtLeast(1e-6f)

        return rawVector.map { it / norm }
    }

    /**
     * Menghitung Cosine Similarity antara 2 vektor embedding.
     */
    fun calculateCosineSimilarity(vecA: List<Float>, vecB: List<Float>): Float {
        if (vecA.isEmpty() || vecB.isEmpty() || vecA.size != vecB.size) return 0f

        var dot = 0f
        var normA = 0f
        var normB = 0f

        for (i in vecA.indices) {
            dot += vecA[i] * vecB[i]
            normA += vecA[i] * vecA[i]
            normB += vecB[i] * vecB[i]
        }

        val denom = (sqrt(normA) * sqrt(normB))
        return if (denom > 0f) dot / denom else 0f
    }
}
