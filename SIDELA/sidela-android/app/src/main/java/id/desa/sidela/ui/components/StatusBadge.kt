package id.desa.sidela.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.theme.*

@Composable
fun StatusBadge(
    status: String,
    modifier: Modifier = Modifier
) {
    val (bgColor, textColor, label) = when (status.uppercase()) {
        "BACKLOG" -> Triple(BadgeBacklogBg, BadgeBacklogText, "Backlog")
        "ON_PROGRESS", "ON PROGRESS", "IN_PROGRESS" -> Triple(BadgeProgressBg, BadgeProgressText, "On Progress")
        "COMPLETED", "SELESAI", "HADIR", "APPROVED" -> Triple(BadgeSuccessBg, BadgeSuccessText, if (status.equals("APPROVED", true)) "Approved" else if (status.equals("HADIR", true)) "Hadir" else "Selesai")
        "ON_HOLD", "ON HOLD", "TERTUNDA" -> Triple(BadgeHoldBg, BadgeHoldText, "On hold")
        "TERLAMBAT", "REJECTED", "DECLINE" -> Triple(BadgeHoldBg, BadgeHoldText, if (status.equals("DECLINE", true) || status.equals("REJECTED", true)) "Decline" else "Terlambat")
        "IZIN", "CUTI", "DINAS_LUAR" -> Triple(BadgeInfoBg, BadgeInfoText, status.replace('_', ' '))
        else -> Triple(BadgeBacklogBg, BadgeBacklogText, status)
    }

    Box(
        modifier = modifier
            .background(color = bgColor, shape = RoundedCornerShape(20.dp))
            .padding(horizontal = 10.dp, vertical = 4.dp)
    ) {
        Text(
            text = label,
            color = textColor,
            fontSize = 11.sp,
            fontWeight = FontWeight.SemiBold
        )
    }
}
