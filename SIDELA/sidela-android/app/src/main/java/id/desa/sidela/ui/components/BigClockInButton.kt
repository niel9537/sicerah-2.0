package id.desa.sidela.ui.components

import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.TouchApp
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.scale
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.theme.EmeraldGradientEnd
import id.desa.sidela.ui.theme.EmeraldGradientStart
import id.desa.sidela.ui.theme.EmeraldPrimary

@Composable
fun BigClockInButton(
    isClockedIn: Boolean = false,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val infiniteTransition = rememberInfiniteTransition(label = "pulse")
    val pulseScale by infiniteTransition.animateFloat(
        initialValue = 1.0f,
        targetValue = 1.06f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 1400, easing = FastOutSlowInEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "pulseScale"
    )

    val labelText = if (isClockedIn) "Clock Out" else "Clock In"
    val gradientColors = if (isClockedIn) {
        listOf(Color(0xFFD97706), Color(0xFFB45309))
    } else {
        listOf(EmeraldGradientStart, EmeraldGradientEnd)
    }

    Box(
        modifier = modifier
            .size(220.dp),
        contentAlignment = Alignment.Center
    ) {
        // Outer glowing pulse ring
        Box(
            modifier = Modifier
                .size(200.dp)
                .scale(pulseScale)
                .background(
                    color = (if (isClockedIn) Color(0xFFD97706) else EmeraldPrimary).copy(alpha = 0.12f),
                    shape = CircleShape
                )
        )

        // Main Giant Circular Button
        Box(
            modifier = Modifier
                .size(175.dp)
                .shadow(
                    elevation = 20.dp,
                    shape = CircleShape,
                    ambientColor = (if (isClockedIn) Color(0xFFD97706) else EmeraldPrimary).copy(alpha = 0.35f),
                    spotColor = (if (isClockedIn) Color(0xFFD97706) else EmeraldPrimary).copy(alpha = 0.5f)
                )
                .background(
                    brush = Brush.verticalGradient(gradientColors),
                    shape = CircleShape
                )
                .clickable(
                    interactionSource = remember { MutableInteractionSource() },
                    indication = null,
                    onClick = onClick
                ),
            contentAlignment = Alignment.Center
        ) {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Icon(
                    imageVector = Icons.Default.TouchApp,
                    contentDescription = labelText,
                    tint = Color.White,
                    modifier = Modifier.size(36.dp)
                )
                Spacer(modifier = Modifier.height(6.dp))
                Text(
                    text = labelText,
                    color = Color.White,
                    fontSize = 17.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}
