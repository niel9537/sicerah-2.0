package id.desa.sidela.ui.screens.journal

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.components.SidelaBottomNavBar
import id.desa.sidela.ui.components.TaskCard
import id.desa.sidela.ui.theme.*

@Composable
fun JournalListScreen(
    viewModel: JournalViewModel,
    onNavigateBack: () -> Unit,
    onNavigateCreate: () -> Unit,
    onNavigateHome: () -> Unit,
    onNavigateAttendance: () -> Unit,
    onNavigateVillageData: () -> Unit,
    onNavigateProfile: () -> Unit,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()
    val filterTabs = listOf("ALL", "BACKLOG", "ON_PROGRESS", "COMPLETED", "ON_HOLD")

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
                    text = "Jurnal Kerja Harian",
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
                        contentDescription = "Tambah Aktivitas",
                        tint = Color.White,
                        modifier = Modifier.size(22.dp)
                    )
                }
            }
        },
        bottomBar = {
            SidelaBottomNavBar(
                currentRoute = "journal",
                onNavigate = { route ->
                    when (route) {
                        "dashboard" -> onNavigateHome()
                        "attendance" -> onNavigateAttendance()
                        "villagedata" -> onNavigateVillageData()
                        "profile" -> onNavigateProfile()
                    }
                }
            )
        },
        containerColor = BackgroundOffWhite
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .padding(horizontal = 20.dp)
        ) {
            Spacer(modifier = Modifier.height(10.dp))

            // Filter Tabs
            LazyRow(
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(filterTabs) { tab ->
                    val isSelected = uiState.selectedFilter == tab
                    val label = when (tab) {
                        "ALL" -> "Semua"
                        "BACKLOG" -> "Backlog"
                        "ON_PROGRESS" -> "On Progress"
                        "COMPLETED" -> "Selesai"
                        "ON_HOLD" -> "On Hold"
                        else -> tab
                    }

                    Box(
                        modifier = Modifier
                            .background(
                                color = if (isSelected) EmeraldPrimary else SurfaceWhite,
                                shape = RoundedCornerShape(20.dp)
                            )
                            .border(
                                width = 1.dp,
                                color = if (isSelected) EmeraldPrimary else CardBorder,
                                shape = RoundedCornerShape(20.dp)
                            )
                            .clickable { viewModel.loadJournals(tab) }
                            .padding(horizontal = 14.dp, vertical = 8.dp)
                    ) {
                        Text(
                            text = label,
                            color = if (isSelected) Color.White else SoftBlack,
                            fontSize = 12.sp,
                            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(18.dp))

            // Journal Tasks List
            LazyColumn(
                modifier = Modifier.fillMaxSize(),
                verticalArrangement = Arrangement.spacedBy(14.dp)
            ) {
                items(uiState.items) { task ->
                    TaskCard(
                        title = task.title,
                        dateText = task.date,
                        timeText = "${task.startTime} - ${task.endTime} WIB",
                        status = task.status,
                        onClick = { },
                        modifier = Modifier.fillMaxWidth()
                    )
                }

                item {
                    Spacer(modifier = Modifier.height(30.dp))
                }
            }
        }
    }
}
