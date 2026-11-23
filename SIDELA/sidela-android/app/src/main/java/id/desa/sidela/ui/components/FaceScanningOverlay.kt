package id.desa.sidela.ui.components

import androidx.compose.animation.core.*
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Cameraswitch
import androidx.compose.material.icons.filled.FlashOn
import androidx.compose.material.icons.filled.QrCodeScanner
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.CornerRadius
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.theme.EmeraldAccent
import id.desa.sidela.ui.theme.EmeraldPrimary

@Composable
fun FaceScanningOverlay(
    instructionText: String,
    onBack: () -> Unit,
    onSwitchCamera: () -> Unit,
    onCapture: () -> Unit,
    modifier: Modifier = Modifier
) {
    val infiniteTransition = rememberInfiniteTransition(label = "laserScan")
    val laserProgress by infiniteTransition.animateFloat(
        initialValue = 0.05f,
        targetValue = 0.95f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 2000, easing = LinearEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "laserProgress"
    )

    Box(
        modifier = modifier
            .fillMaxSize()
    ) {
        // 1. Top Controls Bar
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .statusBarsPadding()
                .padding(horizontal = 20.dp, vertical = 12.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(44.dp)
                    .background(Color.Black.copy(alpha = 0.45f), shape = CircleShape)
                    .clickable { onBack() },
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Kembali",
                    tint = Color.White,
                    modifier = Modifier.size(22.dp)
                )
            }

            Box(
                modifier = Modifier
                    .size(44.dp)
                    .background(Color.Black.copy(alpha = 0.45f), shape = CircleShape)
                    .clickable { onSwitchCamera() },
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.Cameraswitch,
                    contentDescription = "Ganti Kamera",
                    tint = Color.White,
                    modifier = Modifier.size(22.dp)
                )
            }
        }

        // 2. Central High-Tech Face Scanning Frame & Brackets
        Box(
            modifier = Modifier
                .align(Alignment.Center)
                .size(width = 300.dp, height = 370.dp),
            contentAlignment = Alignment.Center
        ) {
            Canvas(modifier = Modifier.fillMaxSize()) {
                val strokeWidth = 5.dp.toPx()
                val cornerLength = 40.dp.toPx()
                val bracketColor = Color.White.copy(alpha = 0.9f)
                val w = size.width
                val h = size.height

                // Top-Left Corner
                drawLine(bracketColor, Offset(0f, 0f), Offset(cornerLength, 0f), strokeWidth)
                drawLine(bracketColor, Offset(0f, 0f), Offset(0f, cornerLength), strokeWidth)

                // Top-Right Corner
                drawLine(bracketColor, Offset(w, 0f), Offset(w - cornerLength, 0f), strokeWidth)
                drawLine(bracketColor, Offset(w, 0f), Offset(w, cornerLength), strokeWidth)

                // Bottom-Left Corner
                drawLine(bracketColor, Offset(0f, h), Offset(cornerLength, h), strokeWidth)
                drawLine(bracketColor, Offset(0f, h), Offset(0f, h - cornerLength), strokeWidth)

                // Bottom-Right Corner
                drawLine(bracketColor, Offset(w, h), Offset(w - cornerLength, h), strokeWidth)
                drawLine(bracketColor, Offset(w, h), Offset(w, h - cornerLength), strokeWidth)

                // Laser Scanning Line with Gradient Glow
                val currentLaserY = h * laserProgress
                drawLine(
                    brush = Brush.horizontalGradient(
                        listOf(
                            Color.Transparent,
                            EmeraldAccent.copy(alpha = 0.85f),
                            Color.White,
                            EmeraldAccent.copy(alpha = 0.85f),
                            Color.Transparent
                        )
                    ),
                    start = Offset(10f, currentLaserY),
                    end = Offset(w - 10f, currentLaserY),
                    strokeWidth = 3.dp.toPx()
                )
            }
        }

        // 3. Instruction Status Pill
        Box(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .padding(bottom = 125.dp)
                .background(Color.Black.copy(alpha = 0.60f), shape = RoundedCornerShape(24.dp))
                .padding(horizontal = 22.dp, vertical = 10.dp)
        ) {
            Text(
                text = instructionText,
                color = Color.White,
                fontSize = 13.sp,
                fontWeight = FontWeight.Medium,
                textAlign = TextAlign.Center
            )
        }

        // 4. Rounded Square Emerald Shutter Button at Bottom Center
        Box(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .padding(bottom = 36.dp)
                .size(68.dp)
                .background(EmeraldPrimary, shape = RoundedCornerShape(20.dp))
                .clickable { onCapture() },
            contentAlignment = Alignment.Center
        ) {
            Icon(
                imageVector = Icons.Default.QrCodeScanner,
                contentDescription = "Pindai Wajah",
                tint = Color.White,
                modifier = Modifier.size(32.dp)
            )
        }
    }
}
