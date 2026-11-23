package id.desa.sidela.ui.screens.villagedata

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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.components.SidelaBottomNavBar
import id.desa.sidela.ui.theme.*

@Composable
fun VillageDataHubScreen(
    onNavigateBack: () -> Unit,
    onNavigateHome: () -> Unit,
    onNavigateAttendance: () -> Unit,
    onNavigateJournal: () -> Unit,
    onNavigateProfile: () -> Unit,
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
                    text = "Pengelolaan Data Desa",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
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
            // Village Info Banner
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(EmeraldLight, shape = RoundedCornerShape(22.dp))
                        .border(1.dp, EmeraldPrimary.copy(alpha = 0.2f), shape = RoundedCornerShape(22.dp))
                        .padding(18.dp)
                ) {
                    Column {
                        Text(
                            text = "Pemerintah Desa Iraonogela",
                            fontSize = 17.sp,
                            fontWeight = FontWeight.Bold,
                            color = EmeraldDark
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Kecamatan Mandrehe, Kabupaten Nias Barat, Sumatera Utara (Kode Pos 22862)",
                            fontSize = 12.sp,
                            color = CharcoalDark
                        )
                    }
                }
            }

            item {
                Text(
                    text = "Modul Layanan & Informasi",
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            // Hub Modules
            item {
                ModuleCard(
                    title = "Data Penduduk & Keluarga",
                    subtitle = "1,265 Penduduk • 3 Wilayah Dusun",
                    icon = Icons.Outlined.People,
                    iconBg = Color(0xFFDBEAFE),
                    iconTint = Color(0xFF2563EB),
                    onClick = { }
                )
            }

            item {
                ModuleCard(
                    title = "Data Perangkat Desa",
                    subtitle = "Kepala Desa, BPD, Kaur, Kasi, dan Kadus",
                    icon = Icons.Outlined.Badge,
                    iconBg = Color(0xFFD1FAE5),
                    iconTint = EmeraldPrimary,
                    onClick = { }
                )
            }

            item {
                ModuleCard(
                    title = "Arsip Surat Masuk & Keluar",
                    subtitle = "Penomoran, disposisi, dan berkas surat",
                    icon = Icons.Outlined.MarkEmailRead,
                    iconBg = Color(0xFFFEF3C7),
                    iconTint = GoldAccent,
                    onClick = { }
                )
            }

            item {
                ModuleCard(
                    title = "Inventaris & Aset Desa",
                    subtitle = "Gedung, kendaraan dinas, dan peralatan",
                    icon = Icons.Outlined.Inventory2,
                    iconBg = Color(0xFFF3E8FF),
                    iconTint = Color(0xFF9333EA),
                    onClick = { }
                )
            }

            item {
                ModuleCard(
                    title = "Profil & Struktur Organisasi",
                    subtitle = "Visi, misi, sejarah, dan kontak kantor desa",
                    icon = Icons.Outlined.AccountBalance,
                    iconBg = Color(0xFFFCE7F3),
                    iconTint = Color(0xFFDB2777),
                    onClick = { }
                )
            }

            item {
                Spacer(modifier = Modifier.height(24.dp))
            }
        }
    }
}

@Composable
private fun ModuleCard(
    title: String,
    subtitle: String,
    icon: ImageVector,
    iconBg: Color,
    iconTint: Color,
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
                    .size(48.dp)
                    .background(iconBg, shape = RoundedCornerShape(14.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = iconTint,
                    modifier = Modifier.size(24.dp)
                )
            }

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = title,
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = subtitle,
                    fontSize = 12.sp,
                    color = TextMuted
                )
            }

            Icon(
                imageVector = Icons.Outlined.ChevronRight,
                contentDescription = null,
                tint = TextLight,
                modifier = Modifier.size(20.dp)
            )
        }
    }
}
