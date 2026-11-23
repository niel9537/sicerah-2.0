package id.desa.sidela.ui.screens.leave

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.components.SidelaBottomNavBar
import id.desa.sidela.ui.components.StatCounterCard
import id.desa.sidela.ui.components.StatusBadge
import id.desa.sidela.ui.theme.*

@Composable
fun LeaveListScreen(
    viewModel: LeaveViewModel,
    onNavigateBack: () -> Unit,
    onNavigateCreate: () -> Unit,
    onNavigateHome: () -> Unit,
    onNavigateAttendance: () -> Unit,
    onNavigateJournal: () -> Unit,
    onNavigateProfile: () -> Unit,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()
    val counters = uiState.counters

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
                    text = "Pengajuan & Izin",
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )

                Box(
                    modifier = Modifier
                        .size(40.dp)
                        .background(EmeraldPrimary, shape = CircleShape)
                        .clickable { onNavigateCreate() },
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.Add,
                        contentDescription = "Buat Pengajuan",
                        tint = Color.White,
                        modifier = Modifier.size(22.dp)
                    )
                }
            }
        },
        bottomBar = {
            SidelaBottomNavBar(
                currentRoute = "villagedata",
                onNavigate = { route ->
                    when (route) {
                        "dashboard" -> onNavigateHome()
                        "attendance" -> onNavigateAttendance()
                        "journal" -> onNavigateJournal()
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
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Counters Row (25 Request, 8 Approved, 16 Decline)
            item {
                Spacer(modifier = Modifier.height(6.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    StatCounterCard(
                        count = counters.totalRequests,
                        label = "Request",
                        countColor = EmeraldPrimary,
                        modifier = Modifier.weight(1f)
                    )
                    StatCounterCard(
                        count = counters.approved,
                        label = "Approved",
                        countColor = EmeraldAccent,
                        modifier = Modifier.weight(1f)
                    )
                    StatCounterCard(
                        count = counters.rejected,
                        label = "Decline",
                        countColor = Color(0xFFEF4444),
                        modifier = Modifier.weight(1f)
                    )
                }
            }

            item {
                Text(
                    text = "Daftar Riwayat Pengajuan",
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            // Leaves List
            items(uiState.items) { item ->
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
                        .padding(16.dp)
                ) {
                    Column {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = item.leaveType.replace('_', ' '),
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = SoftBlack
                            )
                            StatusBadge(status = item.status)
                        }

                        Spacer(modifier = Modifier.height(6.dp))

                        Text(
                            text = "${item.startDate} s/d ${item.endDate} (${item.totalDays} hari)",
                            fontSize = 12.sp,
                            color = TextMuted
                        )

                        Spacer(modifier = Modifier.height(8.dp))

                        Text(
                            text = "Alasan: ${item.reason}",
                            fontSize = 13.sp,
                            color = CharcoalDark
                        )

                        if (!item.rejectionNotes.isNullOrBlank()) {
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = "Catatan: ${item.rejectionNotes}",
                                fontSize = 12.sp,
                                color = Color(0xFFDC2626)
                            )
                        }
                    }
                }
            }

            item {
                Spacer(modifier = Modifier.height(24.dp))
            }
        }
    }
}
