package id.desa.sidela.ui.screens.profile

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import id.desa.sidela.ui.components.SidelaBottomNavBar
import id.desa.sidela.ui.theme.*

@Composable
fun ProfileScreen(
    onNavigateBack: () -> Unit,
    onNavigateHome: () -> Unit,
    onNavigateAttendance: () -> Unit,
    onNavigateJournal: () -> Unit,
    onNavigateVillageData: () -> Unit,
    onNavigateAdmin: () -> Unit,
    onNavigateReports: () -> Unit,
    onLogout: () -> Unit,
    modifier: Modifier = Modifier
) {
    Scaffold(
        topBar = {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(horizontal = 20.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Profil Aparatur",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }
        },
        bottomBar = {
            SidelaBottomNavBar(
                currentRoute = "profile",
                onNavigate = { route ->
                    when (route) {
                        "dashboard" -> onNavigateHome()
                        "attendance" -> onNavigateAttendance()
                        "journal" -> onNavigateJournal()
                        "villagedata" -> onNavigateVillageData()
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
            // User Profile Card
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite, shape = RoundedCornerShape(24.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(24.dp))
                        .padding(20.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        AsyncImage(
                            model = "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400",
                            contentDescription = "Foto Profil",
                            modifier = Modifier
                                .size(88.dp)
                                .clip(CircleShape)
                                .border(3.dp, EmeraldLight, CircleShape)
                        )

                        Spacer(modifier = Modifier.height(12.dp))

                        Text(
                            text = "Rachel Kim",
                            fontSize = 19.sp,
                            fontWeight = FontWeight.Bold,
                            color = SoftBlack
                        )

                        Text(
                            text = "Kaur Tata Usaha & Umum",
                            fontSize = 13.sp,
                            color = TextMuted
                        )

                        Spacer(modifier = Modifier.height(10.dp))

                        // Face Biometric Enrolled Badge
                        Box(
                            modifier = Modifier
                                .background(BadgeSuccessBg, shape = RoundedCornerShape(20.dp))
                                .padding(horizontal = 12.dp, vertical = 6.dp)
                        ) {
                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(6.dp)
                            ) {
                                Icon(
                                    imageVector = Icons.Outlined.Face,
                                    contentDescription = null,
                                    tint = EmeraldPrimary,
                                    modifier = Modifier.size(16.dp)
                                )
                                Text(
                                    text = "Wajah Terdaftar (Aktif)",
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold,
                                    color = EmeraldPrimary
                                )
                            }
                        }
                    }
                }
            }

            // Menu Items
            item {
                Text(
                    text = "Pengaturan & Layanan",
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            item {
                ProfileMenuItem(
                    icon = Icons.Outlined.Assessment,
                    title = "Laporan & Ekspor PDF / Excel",
                    subtitle = "Unduh rekapitulasi presensi dan jurnal kerja",
                    onClick = onNavigateReports
                )
            }

            item {
                ProfileMenuItem(
                    icon = Icons.Outlined.AdminPanelSettings,
                    title = "Pengaturan Administrator Desa",
                    subtitle = "Radius GPS, jam kerja, toleransi, reset wajah",
                    onClick = onNavigateAdmin
                )
            }

            item {
                ProfileMenuItem(
                    icon = Icons.Outlined.LockReset,
                    title = "Ubah Kata Sandi",
                    subtitle = "Perbarui keamanan akun Anda",
                    onClick = { }
                )
            }

            item {
                ProfileMenuItem(
                    icon = Icons.Outlined.Logout,
                    title = "Keluar dari Akun",
                    subtitle = "Hapus sesi perangkat ini",
                    iconTint = Color(0xFFEF4444),
                    onClick = onLogout
                )
            }

            item {
                Spacer(modifier = Modifier.height(24.dp))
            }
        }
    }
}

@Composable
private fun ProfileMenuItem(
    icon: ImageVector,
    title: String,
    subtitle: String,
    iconTint: Color = EmeraldPrimary,
    onClick: () -> Unit
) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
            .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
            .clickable { onClick() }
            .padding(16.dp)
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            Box(
                modifier = Modifier
                    .size(42.dp)
                    .background(BackgroundOffWhite, shape = RoundedCornerShape(12.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = iconTint,
                    modifier = Modifier.size(22.dp)
                )
            }

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = title,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = subtitle,
                    fontSize = 11.sp,
                    color = TextMuted
                )
            }

            Icon(
                imageVector = Icons.Outlined.ChevronRight,
                contentDescription = null,
                tint = TextLight,
                modifier = Modifier.size(18.dp)
            )
        }
    }
}
