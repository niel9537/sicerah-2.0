package id.desa.sidela.ui.screens.facescan

import android.view.ViewGroup
import androidx.camera.core.CameraSelector
import androidx.camera.core.ImageAnalysis
import androidx.camera.core.Preview
import androidx.camera.lifecycle.ProcessCameraProvider
import androidx.camera.view.PreviewView
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import id.desa.sidela.ui.components.FaceScanningOverlay
import id.desa.sidela.ui.theme.EmeraldPrimary
import id.desa.sidela.ui.theme.SoftBlack
import id.desa.sidela.utils.FaceAnalyzer
import java.util.concurrent.Executors

@Composable
fun FaceScanScreen(
    viewModel: FaceScanViewModel,
    isClockOut: Boolean = false,
    onNavigateBack: () -> Unit,
    onScanSuccess: () -> Unit,
    modifier: Modifier = Modifier
) {
    val context = LocalContext.current
    val lifecycleOwner = LocalLifecycleOwner.current

    val scanState by viewModel.scanState.collectAsState()
    val instructionText by viewModel.instruction.collectAsState()

    var cameraSelector by remember { mutableStateOf(CameraSelector.DEFAULT_FRONT_CAMERA) }
    val cameraExecutor = remember { Executors.newSingleThreadExecutor() }

    Box(
        modifier = modifier
            .fillMaxSize()
            .background(Color.Black)
    ) {
        // 1. Fullscreen CameraX Preview
        AndroidView(
            factory = { ctx ->
                val previewView = PreviewView(ctx).apply {
                    layoutParams = ViewGroup.LayoutParams(
                        ViewGroup.LayoutParams.MATCH_PARENT,
                        ViewGroup.LayoutParams.MATCH_PARENT
                    )
                    scaleType = PreviewView.ScaleType.FILL_CENTER
                }

                val cameraProviderFuture = ProcessCameraProvider.getInstance(ctx)
                cameraProviderFuture.addListener({
                    val cameraProvider = cameraProviderFuture.get()
                    val preview = Preview.Builder().build().also {
                        it.setSurfaceProvider(previewView.surfaceProvider)
                    }

                    val imageAnalyzer = ImageAnalysis.Builder()
                        .setBackpressureStrategy(ImageAnalysis.STRATEGY_KEEP_ONLY_LATEST)
                        .build()
                        .also {
                            it.setAnalyzer(
                                cameraExecutor,
                                FaceAnalyzer(
                                    onFaceDetected = { face, isBlink, score ->
                                        viewModel.onFaceDetected(face, isBlink, score)
                                    },
                                    onError = { /* Log or ignore frame error */ }
                                )
                            )
                        }

                    try {
                        cameraProvider.unbindAll()
                        cameraProvider.bindToLifecycle(
                            lifecycleOwner,
                            cameraSelector,
                            preview,
                            imageAnalyzer
                        )
                    } catch (exc: Exception) {
                        exc.printStackTrace()
                    }
                }, ContextCompat.getMainExecutor(ctx))

                previewView
            },
            modifier = Modifier.fillMaxSize()
        )

        // 2. High-Tech Face Scanning HUD & Laser Line Overlay
        FaceScanningOverlay(
            instructionText = instructionText,
            onBack = onNavigateBack,
            onSwitchCamera = {
                cameraSelector = if (cameraSelector == CameraSelector.DEFAULT_FRONT_CAMERA) {
                    CameraSelector.DEFAULT_BACK_CAMERA
                } else {
                    CameraSelector.DEFAULT_FRONT_CAMERA
                }
            },
            onCapture = {
                viewModel.performScan(context, isClockOut)
            }
        )

        // 3. Processing Loading Indicator
        if (scanState is FaceScanState.Processing) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Color.Black.copy(alpha = 0.65f)),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    CircularProgressIndicator(color = EmeraldPrimary, strokeWidth = 4.dp)
                    Spacer(modifier = Modifier.height(16.dp))
                    Text(
                        text = "Memverifikasi Keaslian & Lokasi GPS...",
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Medium
                    )
                }
            }
        }

        // 4. Result Dialog (Success or Error)
        when (val state = scanState) {
            is FaceScanState.Success -> {
                AlertDialog(
                    onDismissRequest = { },
                    confirmButton = {
                        Button(
                            onClick = {
                                viewModel.resetScan()
                                onScanSuccess()
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary),
                            shape = RoundedCornerShape(16.dp)
                        ) {
                            Text("Selesai")
                        }
                    },
                    title = {
                        Text("Presensi Berhasil", fontWeight = FontWeight.Bold, color = EmeraldPrimary)
                    },
                    text = {
                        Column {
                            Text(state.message, color = SoftBlack, fontSize = 14.sp)
                            Spacer(modifier = Modifier.height(8.dp))
                            Text("Waktu: ${state.record.clockIn ?: state.record.clockOut ?: "--"}", fontSize = 13.sp)
                            Text("Status: ${state.record.status}", fontSize = 13.sp)
                            Text("Akurasi Wajah: ${"%.1f".format(state.record.faceConfidence)}%", fontSize = 13.sp)
                            Text("Jarak Kantor: ${"%.1f".format(state.record.distanceMeters)}m (Dalam Radius)", fontSize = 13.sp)
                        }
                    },
                    shape = RoundedCornerShape(22.dp),
                    containerColor = Color.White
                )
            }
            is FaceScanState.Error -> {
                AlertDialog(
                    onDismissRequest = { viewModel.resetScan() },
                    confirmButton = {
                        Button(
                            onClick = { viewModel.resetScan() },
                            colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary),
                            shape = RoundedCornerShape(16.dp)
                        ) {
                            Text("Coba Lagi")
                        }
                    },
                    title = {
                        Text("Verifikasi Gagal", fontWeight = FontWeight.Bold, color = Color(0xFFEF4444))
                    },
                    text = {
                        Text(state.message, color = SoftBlack, fontSize = 14.sp)
                    },
                    shape = RoundedCornerShape(22.dp),
                    containerColor = Color.White
                )
            }
            else -> {}
        }
    }
}
