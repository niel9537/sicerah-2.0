package id.desa.sidela.ui.screens.dashboard

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Notifications
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import id.desa.sidela.ui.components.*
import id.desa.sidela.ui.theme.*

@Composable
fun TodaySummaryScreen(
    viewModel: DashboardViewModel,
    onNavigateToAttendance: () -> Unit,
    onNavigateToFaceScan: () -> Unit,
    onNavigateToRequests: () -> Unit,
    onNavigateToTasks: () -> Unit,
    onNavigateToProfile: () -> Unit,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()

    when (val state = uiState) {
        is DashboardUiState.Loading -> {
            Box(
                modifier = modifier
                    .fillMaxSize()
                    .background(BackgroundOffWhite),
                contentAlignment = Alignment.Center
            ) {
                CircularProgressIndicator(color = EmeraldPrimary)
            }
        }
        is DashboardUiState.Error -> {
            Box(
                modifier = modifier
                    .fillMaxSize()
                    .background(BackgroundOffWhite)
                    .padding(24.dp),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text(text = state.message, color = SoftBlack, fontSize = 16.sp)
                    Spacer(modifier = Modifier.height(16.dp))
                    Button(
                        onClick = { viewModel.loadDashboard() },
                        colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary)
                    ) {
                        Text("Coba Lagi")
                    }
                }
            }
        }
        is DashboardUiState.Success -> {
            val data = state.data
            val user = data.user
            val todaySummary = data.todaySummary
            val requestCounters = data.requestStatus
            val tasks = data.recentTasks

            Scaffold(
                bottomBar = {
                    SidelaBottomNavBar(
                        currentRoute = "dashboard",
                        onNavigate = { route ->
                            when (route) {
                                "attendance" -> onNavigateToAttendance()
                                "journal" -> onNavigateToTasks()
                                "villagedata" -> onNavigateToRequests()
                                "profile" -> onNavigateToProfile()
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
                    verticalArrangement = Arrangement.spacedBy(20.dp)
                ) {
                    // 1. Top Header: Date, Welcome message, Notification Bell, Profile Avatar
                    item {
                        Spacer(modifier = Modifier.height(10.dp))
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column {
                                Text(
                                    text = "Monday, 26 January",
                                    fontSize = 13.sp,
                                    color = TextMuted,
                                    fontWeight = FontWeight.Medium
                                )
                                Spacer(modifier = Modifier.height(2.dp))
                                Text(
                                    text = "Welcome, ${user.fullName}",
                                    fontSize = 17.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = SoftBlack
                                )
                            }

                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(10.dp)
                            ) {
                                // Notification Bell
                                Box(
                                    modifier = Modifier
                                        .size(40.dp)
                                        .background(SurfaceWhite, shape = CircleShape)
                                        .border(1.dp, CardBorder, shape = CircleShape)
                                        .clickable { },
                                    contentAlignment = Alignment.Center
                                ) {
                                    Icon(
                                        imageVector = Icons.Outlined.Notifications,
                                        contentDescription = "Notifikasi",
                                        tint = SoftBlack,
                                        modifier = Modifier.size(20.dp)
                                    )
                                    // Red dot badge
                                    Box(
                                        modifier = Modifier
                                            .size(7.dp)
                                            .align(Alignment.TopEnd)
                                            .offset(x = (-10).dp, y = (10).dp)
                                            .background(Color(0xFFEF4444), shape = CircleShape)
                                    )
                                }

                                // Profile Avatar
                                AsyncImage(
                                    model = user.avatarUrl ?: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400",
                                    contentDescription = "Avatar Pengguna",
                                    modifier = Modifier
                                        .size(42.dp)
                                        .clip(CircleShape)
                                        .border(2.dp, SurfaceWhite, CircleShape)
                                        .clickable { onNavigateToProfile() }
                                )
                            }
                        }
                    }

                    // 2. Title: Today's Summary
                    item {
                        Text(
                            text = "Today's\nSummary",
                            fontSize = 32.sp,
                            fontWeight = FontWeight.Bold,
                            color = SoftBlack,
                            lineHeight = 36.sp
                        )
                    }

                    // 3. Attendance Summary Card (Matching Mockup with Clock In Now emerald button)
                    item {
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .shadow(
                                    elevation = 6.dp,
                                    shape = RoundedCornerShape(22.dp),
                                    ambientColor = Color.Black.copy(alpha = 0.04f),
                                    spotColor = Color.Black.copy(alpha = 0.08f)
                                )
                                .background(SurfaceWhite, shape = RoundedCornerShape(22.dp))
                                .border(1.dp, CardBorder, shape = RoundedCornerShape(22.dp))
                                .padding(18.dp)
                        ) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                // Clock In Column
                                Column {
                                    Text(
                                        text = "Clock In",
                                        fontSize = 11.sp,
                                        color = TextMuted,
                                        fontWeight = FontWeight.Medium
                                    )
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = todaySummary.clockIn ?: "--",
                                        fontSize = 15.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = SoftBlack
                                    )
                                }

                                // Clock Out Column
                                Column {
                                    Text(
                                        text = "Clock Out",
                                        fontSize = 11.sp,
                                        color = TextMuted,
                                        fontWeight = FontWeight.Medium
                                    )
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = todaySummary.clockOut ?: "--",
                                        fontSize = 15.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = SoftBlack
                                    )
                                }

                                // Clock In Now Emerald Pill Button
                                Button(
                                    onClick = { onNavigateToFaceScan() },
                                    colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary),
                                    shape = RoundedCornerShape(24.dp),
                                    contentPadding = PaddingValues(horizontal = 18.dp, vertical = 10.dp)
                                ) {
                                    Text(
                                        text = if (todaySummary.clockIn == null || todaySummary.clockIn == "--") "Clock In Now" else "Clock Out Now",
                                        fontSize = 12.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color.White
                                    )
                                }
                            }
                        }
                    }

                    // 4. Request Status Section Header & Counter Cards
                    item {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "Request Status",
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                color = SoftBlack
                            )
                            Text(
                                text = "See All",
                                fontSize = 12.sp,
                                fontWeight = FontWeight.SemiBold,
                                color = TextMuted,
                                modifier = Modifier.clickable { onNavigateToRequests() }
                            )
                        }

                        Spacer(modifier = Modifier.height(12.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            StatCounterCard(
                                count = requestCounters.totalRequests,
                                label = "Request",
                                countColor = EmeraldPrimary,
                                onClick = onNavigateToRequests,
                                modifier = Modifier.weight(1f)
                            )
                            StatCounterCard(
                                count = requestCounters.approved,
                                label = "Approved",
                                countColor = EmeraldAccent,
                                onClick = onNavigateToRequests,
                                modifier = Modifier.weight(1f)
                            )
                            StatCounterCard(
                                count = requestCounters.rejected,
                                label = "Decline",
                                countColor = Color(0xFFEF4444),
                                onClick = onNavigateToRequests,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }

                    // 5. Task Section Header
                    item {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "Task",
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                color = SoftBlack
                            )
                            Text(
                                text = "See All",
                                fontSize = 12.sp,
                                fontWeight = FontWeight.SemiBold,
                                color = TextMuted,
                                modifier = Modifier.clickable { onNavigateToTasks() }
                            )
                        }
                    }

                    // 6. Task Cards List (Matching 2x2 cards from screenshot)
                    items(tasks.chunked(2)) { rowTasks ->
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            rowTasks.forEach { task ->
                                TaskCard(
                                    title = task.title,
                                    dateText = task.date,
                                    timeText = "${task.startTime} - ${task.endTime}",
                                    status = task.status,
                                    onClick = onNavigateToTasks,
                                    modifier = Modifier.weight(1f)
                                )
                            }
                            if (rowTasks.size == 1) {
                                Spacer(modifier = Modifier.weight(1f))
                            }
                        }
                    }

                    item {
                        Spacer(modifier = Modifier.height(20.dp))
                    }
                }
            }
        }
    }
}
