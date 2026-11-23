package id.desa.sidela.ui.screens.report

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.outlined.Description
import androidx.compose.material.icons.outlined.FileDownload
import androidx.compose.material.icons.outlined.TableChart
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import id.desa.sidela.ui.theme.*

@Composable
fun ReportScreen(
    onNavigateBack: () -> Unit,
    modifier: Modifier = Modifier
) {
    var selectedPeriod by remember { mutableStateOf("Bulan Ini (Januari 2026)") }
    var showExportDialog by remember { mutableStateOf(false) }
    var exportTypeMessage by remember { mutableStateOf("") }

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
                    text = "Laporan & Rekapitulasi",
                    fontSize = 17.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )

                Spacer(modifier = Modifier.size(40.dp))
            }
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
            // Period selector card
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
                        .padding(18.dp)
                ) {
                    Column {
                        Text(
                            text = "Periode Laporan Terpilih",
                            fontSize = 12.sp,
                            color = TextMuted
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = selectedPeriod,
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Bold,
                            color = SoftBlack
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Kop Resmi: Pemerintah Desa Iraonogela, Kec. Mandrehe, Kab. Nias Barat",
                            fontSize = 11.sp,
                            color = EmeraldPrimary,
                            fontWeight = FontWeight.Medium
                        )
                    }
                }
            }

            item {
                Text(
                    text = "Format Dokumen Ekspor",
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            item {
                ExportOptionCard(
                    title = "Ekspor Dokumen PDF Resmi",
                    description = "Format rapi lengkap dengan Kop Surat Pemerintah Desa Iraonogela, tabel kehadiran, dan tanda tangan Kepala Desa.",
                    icon = Icons.Outlined.Description,
                    buttonText = "Unduh PDF",
                    buttonColor = Color(0xFFDC2626),
                    onExport = {
                        exportTypeMessage = "Laporan Presensi format PDF berhasil diunduh ke folder Downloads."
                        showExportDialog = true
                    }
                )
            }

            item {
                ExportOptionCard(
                    title = "Ekspor Lembar Kerja Excel / CSV",
                    description = "Data tabular untuk pengolahan lebih lanjut, rekapitulasi keterlambatan, dan integrasi sistem penggajian/tunjangan.",
                    icon = Icons.Outlined.TableChart,
                    buttonText = "Unduh Excel/CSV",
                    buttonColor = EmeraldPrimary,
                    onExport = {
                        exportTypeMessage = "Laporan format Excel/CSV berhasil diunduh ke folder Downloads."
                        showExportDialog = true
                    }
                )
            }

            item {
                Spacer(modifier = Modifier.height(24.dp))
            }
        }

        if (showExportDialog) {
            AlertDialog(
                onDismissRequest = { showExportDialog = false },
                confirmButton = {
                    Button(
                        onClick = { showExportDialog = false },
                        colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary)
                    ) {
                        Text("Selesai")
                    }
                },
                title = { Text("Ekspor Berhasil", fontWeight = FontWeight.Bold, color = EmeraldPrimary) },
                text = { Text(exportTypeMessage) },
                shape = RoundedCornerShape(20.dp)
            )
        }
    }
}

@Composable
private fun ExportOptionCard(
    title: String,
    description: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    buttonText: String,
    buttonColor: Color,
    onExport: () -> Unit
) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .background(SurfaceWhite, shape = RoundedCornerShape(20.dp))
            .border(1.dp, CardBorder, shape = RoundedCornerShape(20.dp))
            .padding(18.dp)
    ) {
        Column {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                Box(
                    modifier = Modifier
                        .size(44.dp)
                        .background(buttonColor.copy(alpha = 0.1f), shape = RoundedCornerShape(12.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = icon,
                        contentDescription = null,
                        tint = buttonColor,
                        modifier = Modifier.size(24.dp)
                    )
                }

                Text(
                    text = title,
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            Spacer(modifier = Modifier.height(10.dp))

            Text(
                text = description,
                fontSize = 12.sp,
                color = TextMuted,
                lineHeight = 16.sp
            )

            Spacer(modifier = Modifier.height(16.dp))

            Button(
                onClick = onExport,
                colors = ButtonDefaults.buttonColors(containerColor = buttonColor),
                shape = RoundedCornerShape(14.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Icon(Icons.Outlined.FileDownload, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(modifier = Modifier.width(8.dp))
                Text(buttonText, fontWeight = FontWeight.Bold)
            }
        }
    }
}
