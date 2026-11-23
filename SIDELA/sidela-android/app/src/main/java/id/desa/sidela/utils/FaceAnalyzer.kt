package id.desa.sidela.utils

import android.annotation.SuppressLint
import androidx.camera.core.ImageAnalysis
import androidx.camera.core.ImageProxy
import com.google.mlkit.vision.common.InputImage
import com.google.mlkit.vision.face.Face
import com.google.mlkit.vision.face.FaceDetection
import com.google.mlkit.vision.face.FaceDetectorOptions

class FaceAnalyzer(
    private val onFaceDetected: (Face, Boolean, Float) -> Unit,
    private val onError: (Exception) -> Unit
) : ImageAnalysis.Analyzer {

    private val detector = FaceDetection.getClient(
        FaceDetectorOptions.Builder()
            .setPerformanceMode(FaceDetectorOptions.PERFORMANCE_MODE_ACCURATE)
            .setLandmarkMode(FaceDetectorOptions.LANDMARK_MODE_ALL)
            .setClassificationMode(FaceDetectorOptions.CLASSIFICATION_MODE_ALL)
            .setMinFaceSize(0.25f)
            .enableTracking()
            .build()
    )

    // Liveness blink state tracking
    private var wasEyeClosed = false
    private var blinkDetected = false
    private var livenessScore = 0.5f

    @SuppressLint("UnsafeOptInUsageError")
    override fun analyze(imageProxy: ImageProxy) {
        val mediaImage = imageProxy.image
        if (mediaImage != null) {
            val image = InputImage.fromMediaImage(mediaImage, imageProxy.imageInfo.rotationDegrees)
            detector.process(image)
                .addOnSuccessListener { faces ->
                    if (faces.isNotEmpty()) {
                        val mainFace = faces.maxByOrNull { it.boundingBox.width() * it.boundingBox.height() }
                        if (mainFace != null) {
                            val leftEyeOpen = mainFace.leftEyeOpenProbability ?: -1f
                            val rightEyeOpen = mainFace.rightEyeOpenProbability ?: -1f

                            // Blink Detection state machine
                            if (leftEyeOpen in 0.0f..0.25f && rightEyeOpen in 0.0f..0.25f) {
                                wasEyeClosed = true
                            } else if (wasEyeClosed && leftEyeOpen > 0.70f && rightEyeOpen > 0.70f) {
                                blinkDetected = true
                                wasEyeClosed = false
                                livenessScore = 0.95f
                            }

                            // Head orientation check (prevents flat photos)
                            val rotY = mainFace.headEulerAngleY
                            val rotZ = mainFace.headEulerAngleZ
                            val isFacingForward = kotlin.math.abs(rotY) < 20.0f && kotlin.math.abs(rotZ) < 20.0f

                            if (blinkDetected && isFacingForward) {
                                livenessScore = 0.98f
                            }

                            onFaceDetected(mainFace, blinkDetected, livenessScore)
                        }
                    }
                }
                .addOnFailureListener { e ->
                    onError(e)
                }
                .addOnCompleteListener {
                    imageProxy.close()
                }
        } else {
            imageProxy.close()
        }
    }
}
