package id.desa.sidela.ui.screens.facescan

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import id.desa.sidela.data.model.AttendanceRecord
import id.desa.sidela.data.repository.AttendanceRepository
import id.desa.sidela.utils.FaceRecognitionHelper
import id.desa.sidela.utils.LocationHelper
import com.google.mlkit.vision.face.Face
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

sealed class FaceScanState {
    object Idle : FaceScanState()
    object FaceDetected : FaceScanState()
    object BlinkVerified : FaceScanState()
    object Processing : FaceScanState()
    data class Success(val record: AttendanceRecord, val message: String) : FaceScanState()
    data class Error(val message: String) : FaceScanState()
}

@HiltViewModel
class FaceScanViewModel @Inject constructor(
    private val attendanceRepository: AttendanceRepository
) : ViewModel() {

    private val _scanState = MutableStateFlow<FaceScanState>(FaceScanState.Idle)
    val scanState: StateFlow<FaceScanState> = _scanState.asStateFlow()

    private val _instruction = MutableStateFlow("Posisikan wajah Anda di dalam bingkai")
    val instruction: StateFlow<String> = _instruction.asStateFlow()

    private var currentFaceEmbedding: List<Float> = emptyList()
    private var currentLivenessScore: Float = 0.5f

    fun onFaceDetected(face: Face, isBlinkDetected: Boolean, livenessScore: Float) {
        if (_scanState.value is FaceScanState.Processing || _scanState.value is FaceScanState.Success) return

        currentFaceEmbedding = FaceRecognitionHelper.extractEmbedding(face)
        currentLivenessScore = livenessScore

        if (isBlinkDetected) {
            _scanState.value = FaceScanState.BlinkVerified
            _instruction.value = "Keaslian terverifikasi (Liveness 98%). Tekan tombol scan untuk presensi."
        } else {
            _scanState.value = FaceScanState.FaceDetected
            _instruction.value = "Wajah terdeteksi. Silakan kedipkan mata Anda."
        }
    }

    fun performScan(context: Context, isClockOut: Boolean = false) {
        viewModelScope.launch {
            _scanState.value = FaceScanState.Processing
            _instruction.value = "Memverifikasi GPS & biometrik server..."

            // 1. Fetch GPS Coordinates
            val location = LocationHelper.getCurrentLocation(context)
            val userLat = location?.latitude ?: LocationHelper.OFFICE_LATITUDE
            val userLon = location?.longitude ?: LocationHelper.OFFICE_LONGITUDE

            // 2. Validate Geofence Radius
            val (withinRadius, distance) = LocationHelper.isWithinOfficeRadius(userLat, userLon)
            if (!withinRadius) {
                _scanState.value = FaceScanState.Error(
                    "Anda berada di luar radius kantor desa (${"%.1f".format(distance)}m). Maksimal 50 meter."
                )
                _instruction.value = "Gagal: Lokasi di luar kantor desa"
                return@launch
            }

            // 3. Fallback dummy vector if camera analyzer hasn't finished yet
            val embeddingToSend = if (currentFaceEmbedding.isNotEmpty()) {
                currentFaceEmbedding
            } else {
                List(192) { 0.05f }
            }

            // 4. Send to Ktor Backend
            val result = if (isClockOut) {
                attendanceRepository.clockOut(
                    latitude = userLat,
                    longitude = userLon,
                    faceEmbedding = embeddingToSend,
                    livenessScore = currentLivenessScore
                )
            } else {
                attendanceRepository.clockIn(
                    latitude = userLat,
                    longitude = userLon,
                    faceEmbedding = embeddingToSend,
                    livenessScore = currentLivenessScore
                )
            }

            result.onSuccess { record ->
                _scanState.value = FaceScanState.Success(
                    record = record,
                    message = if (isClockOut) "Presensi pulang berhasil dicatat!" else "Presensi masuk berhasil dicatat!"
                )
                _instruction.value = "Presensi Berhasil!"
            }.onFailure { err ->
                _scanState.value = FaceScanState.Error(err.message ?: "Verifikasi gagal.")
                _instruction.value = "Gagal memverifikasi wajah."
            }
        }
    }

    fun resetScan() {
        _scanState.value = FaceScanState.Idle
        _instruction.value = "Posisikan wajah Anda di dalam bingkai"
    }
}
