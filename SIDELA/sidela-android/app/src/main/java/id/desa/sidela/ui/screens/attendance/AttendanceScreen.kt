package id.desa.sidela.ui.screens.attendance

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.material.icons.filled.MoreHoriz
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.components.BigClockInButton
import id.desa.sidela.ui.components.SidelaBottomNavBar
import id.desa.sidela.ui.theme.*

@Composable
fun AttendanceScreen(
    viewModel: AttendanceViewModel,
    onNavigateBack: () -> Unit,
    onNavigateToFaceScan: () -> Unit,
    onNavigateHome: () -> Unit,
    onNavigateJournal: () -> Unit,
    onNavigateVillageData: () -> Unit,
    onNavigateProfile: () -> Unit,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()
    val todaySummary = uiState.todaySummary
    val logs = uiState.historyLogs

    Scaffold(
        topBar = {
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
                        .size(40.dp)
                        .background(SurfaceWhite, shape = CircleShape)
                        .border(1.dp, CardBorder, shape = CircleShape)
                        .clickable { onNavigateBack() },
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.ArrowBack,
                        contentDescription = "Kembali",
                        tint = SoftBlack,
                        modifier = Modifier.size(18.dp)
                    )
                }

                Text(
                    text = "Attendance",
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )

                Box(
                    modifier = Modifier
                        .size(40.dp)
                        .background(SurfaceWhite, shape = CircleShape)
                        .border(1.dp, CardBorder, shape = CircleShape)
                        .clickable { },
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.MoreHoriz,
                        contentDescription = "Menu",
                        tint = SoftBlack,
                        modifier = Modifier.size(18.dp)
                    )
                }
            }
        },
        bottomBar = {
            SidelaBottomNavBar(
                currentRoute = "attendance",
                onNavigate = { route ->
                    when (route) {
                        "dashboard" -> onNavigateHome()
                        "journal" -> onNavigateJournal()
                        "villagedata" -> onNavigateVillageData()
                        "profile" -> onNavigateProfile()
                    }
                }
            )
        },
        containerColor = BackgroundOffWhite
    ) { innerPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .padding(horizontal = 20.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // 1. Center Giant Circular Emerald Button
            item {
                Spacer(modifier = Modifier.height(16.dp))
                BigClockInButton(
                    isClockedIn = todaySummary?.clockIn != null && todaySummary.clockIn != "--",
                    onClick = { onNavigateToFaceScan() }
                )
            }

            // 2. Digital Time Display & Office Radius Subtext
            item {
                Spacer(modifier = Modifier.height(18.dp))
                Text(
                    text = todaySummary?.serverTime ?: "08.25",
                    fontSize = 32.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = "Your location must be near in office",
                    fontSize = 12.sp,
                    color = TextMuted,
                    fontWeight = FontWeight.Normal
                )
                Spacer(modifier = Modifier.height(18.dp))
            }

            // 3. Status Metric Card (Clock In, Clock Out, Work Hours, Break Time)
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .shadow(
                            elevation = 4.dp,
                            shape = RoundedCornerShape(20.dp),
                            ambientColor = Color.Black.copy(alpha = 0.04f),
                            spotColor = Color.Black.copy(alpha = 0.06f)
                        )
                        .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
                        .padding(vertical = 16.dp, horizontal = 12.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceAround,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        MetricItem("Clock In", todaySummary?.clockIn ?: "--")
                        MetricItem("Clock Out", todaySummary?.clockOut ?: "--")
                        MetricItem("Work Hours", todaySummary?.workHours ?: "--")
                        MetricItem("Break Time", todaySummary?.breakTime ?: "--")
                    }
                }
                Spacer(modifier = Modifier.height(24.dp))
            }

            // 4. Attendance Log Section Header with "This month v" Filter Dropdown
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Attendance Log",
                        fontSize = 17.sp,
                        fontWeight = FontWeight.Bold,
                        color = SoftBlack
                    )

                    // Filter Pill
                    Box(
                        modifier = Modifier
                            .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
                            .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
                            .clickable { }
                            .padding(horizontal = 12.dp, vertical = 6.dp)
                    ) {
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(4.dp)
                        ) {
                            Text(
                                text = "This month",
                                fontSize = 12.sp,
                                color = SoftBlack,
                                fontWeight = FontWeight.Medium
                            )
                            Icon(
                                imageVector = Icons.Default.KeyboardArrowDown,
                                contentDescription = null,
                                tint = TextMuted,
                                modifier = Modifier.size(16.dp)
                            )
                        }
                    }
                }
                Spacer(modifier = Modifier.height(14.dp))
            }

            // 5. Table Header (Date | Clock In | Clock Out | Work Hours)
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(Color(0xFF8E8E93).copy(alpha = 0.85f), shape = RoundedCornerShape(topStart = 14.dp, topEnd = 14.dp))
                        .padding(vertical = 10.dp, horizontal = 14.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text("Date", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold, modifier = Modifier.weight(1.3f))
                        Text("Clock In", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold, textAlign = TextAlign.Center, modifier = Modifier.weight(1f))
                        Text("Clock Out", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold, textAlign = TextAlign.Center, modifier = Modifier.weight(1f))
                        Text("Work Hours", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold, textAlign = TextAlign.End, modifier = Modifier.weight(1f))
                    }
                }
            }

            // 6. Table Rows matching screenshot (Fri 23 Jan, Thu 22 Jan, Wed 21 Jan, Tue 20 Jan)
            items(logs) { log ->
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite)
                        .border(width = 0.5.dp, color = CardBorder)
                        .padding(vertical = 12.dp, horizontal = 14.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = log.date,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Medium,
                            color = SoftBlack,
                            modifier = Modifier.weight(1.3f)
                        )
                        Text(
                            text = log.clockIn ?: "--",
                            fontSize = 12.sp,
                            color = SoftBlack,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(1f)
                        )
                        Text(
                            text = log.clockOut ?: "--",
                            fontSize = 12.sp,
                            color = SoftBlack,
                            textAlign = TextAlign.Center,
                            modifier = Modifier.weight(1f)
                        )
                        Text(
                            text = log.workHours ?: "--",
                            fontSize = 12.sp,
                            color = SoftBlack,
                            fontWeight = FontWeight.Medium,
                            textAlign = TextAlign.End,
                            modifier = Modifier.weight(1f)
                        )
                    }
                }
            }

            item {
                Spacer(modifier = Modifier.height(28.dp))
            }
        }
    }
}

@Composable
private fun MetricItem(label: String, value: String) {
    Column(
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(
            text = label,
            fontSize = 11.sp,
            color = TextMuted,
            fontWeight = FontWeight.Medium
        )
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = value,
            fontSize = 13.sp,
            fontWeight = FontWeight.Bold,
            color = SoftBlack
        )
    }
}
