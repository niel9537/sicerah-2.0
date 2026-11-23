package id.desa.sidela.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Assignment
import androidx.compose.material.icons.outlined.ChatBubbleOutline
import androidx.compose.material.icons.outlined.CheckCircleOutline
import androidx.compose.material.icons.outlined.People
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import id.desa.sidela.ui.theme.*

@Composable
fun TaskCard(
    title: String,
    dateText: String,
    timeText: String,
    status: String,
    onClick: () -> Unit = {},
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
            .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
            .clickable { onClick() }
            .padding(14.dp)
    ) {
        Column(
            modifier = Modifier.fillMaxWidth()
        ) {
            // Top Row: Icon & Status Badge
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(32.dp)
                        .background(BackgroundOffWhite, shape = RoundedCornerShape(10.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Outlined.Assignment,
                        contentDescription = null,
                        tint = SoftBlack,
                        modifier = Modifier.size(18.dp)
                    )
                }

                StatusBadge(status = status)
            }

            Spacer(modifier = Modifier.height(12.dp))

            // Title
            Text(
                text = title,
                fontSize = 14.sp,
                fontWeight = FontWeight.Bold,
                color = SoftBlack,
                maxLines = 2,
                overflow = TextOverflow.Ellipsis,
                lineHeight = 18.sp
            )

            Spacer(modifier = Modifier.height(6.dp))

            // Subtitle / Date & Time
            Text(
                text = "$dateText\n$timeText",
                fontSize = 11.sp,
                color = TextMuted,
                lineHeight = 15.sp
            )

            Spacer(modifier = Modifier.height(14.dp))

            // Bottom row: Avatar indicators or activity counter
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                Row(
                    horizontalArrangement = Arrangement.spacedBy((-6).dp)
                ) {
                    listOf(
                        "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100",
                        "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100",
                        "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100"
                    ).forEach { url ->
                        AsyncImage(
                            model = url,
                            contentDescription = null,
                            modifier = Modifier
                                .size(22.dp)
                                .clip(CircleShape)
                                .border(1.5.dp, SurfaceWhite, CircleShape)
                        )
                    }
                }

                Spacer(modifier = Modifier.weight(1f))

                // Stats icons
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(4.dp)
                ) {
                    Icon(Icons.Outlined.CheckCircleOutline, contentDescription = null, tint = TextLight, modifier = Modifier.size(13.dp))
                    Text("3", fontSize = 10.sp, color = TextMuted)
                    Spacer(modifier = Modifier.width(4.dp))
                    Icon(Icons.Outlined.ChatBubbleOutline, contentDescription = null, tint = TextLight, modifier = Modifier.size(13.dp))
                    Text("23", fontSize = 10.sp, color = TextMuted)
                }
            }
        }
    }
}
