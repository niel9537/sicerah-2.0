package id.desa.sidela.ui.screens.admin

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Save
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
fun AdminSettingsScreen(
    onNavigateBack: () -> Unit,
    modifier: Modifier = Modifier
) {
    var officeName by remember { mutableStateOf("Kantor Pemerintah Desa Iraonogela") }
    var latitude by remember { mutableStateOf("1.0425000") }
    var longitude by remember { mutableStateOf("97.5258000") }
    var radiusMeters by remember { mutableStateOf("50") }
    var workStartTime by remember { mutableStateOf("08:00") }
    var workEndTime by remember { mutableStateOf("16:00") }
    var lateTolerance by remember { mutableStateOf("15") }

    var showSuccessSnackbar by remember { mutableStateOf(false) }

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
                    text = "Pengaturan Administrator",
                    fontSize = 17.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )

                Box(
                    modifier = Modifier
                        .size(40.dp)
                        .background(EmeraldPrimary, shape = CircleShape)
                        .clickable { showSuccessSnackbar = true },
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.Save,
                        contentDescription = "Simpan",
                        tint = Color.White,
                        modifier = Modifier.size(20.dp)
                    )
                }
            }
        },
        containerColor = BackgroundOffWhite
    ) { innerPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .padding(horizontal = 20.dp),
            verticalArrangement = Arrangement.spacedBy(18.dp)
        ) {
            item {
                Text(
                    text = "Parameter Geofencing GPS & Jam Kerja",
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite, shape = RoundedCornerShape(22.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(22.dp))
                        .padding(20.dp)
                ) {
                    Column(verticalArrangement = Arrangement.spacedBy(14.dp)) {
                        OutlinedTextField(
                            value = officeName,
                            onValueChange = { officeName = it },
                            label = { Text("Nama Lokasi Kantor") },
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(14.dp)
                        )

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            OutlinedTextField(
                                value = latitude,
                                onValueChange = { latitude = it },
                                label = { Text("Latitude") },
                                modifier = Modifier.weight(1f),
                                shape = RoundedCornerShape(14.dp)
                            )
                            OutlinedTextField(
                                value = longitude,
                                onValueChange = { longitude = it },
                                label = { Text("Longitude") },
                                modifier = Modifier.weight(1f),
                                shape = RoundedCornerShape(14.dp)
                            )
                        }

                        OutlinedTextField(
                            value = radiusMeters,
                            onValueChange = { radiusMeters = it },
                            label = { Text("Radius Presensi (Meter)") },
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(14.dp)
                        )

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            OutlinedTextField(
                                value = workStartTime,
                                onValueChange = { workStartTime = it },
                                label = { Text("Jam Masuk") },
                                modifier = Modifier.weight(1f),
                                shape = RoundedCornerShape(14.dp)
                            )
                            OutlinedTextField(
                                value = workEndTime,
                                onValueChange = { workEndTime = it },
                                label = { Text("Jam Pulang") },
                                modifier = Modifier.weight(1f),
                                shape = RoundedCornerShape(14.dp)
                            )
                        }

                        OutlinedTextField(
                            value = lateTolerance,
                            onValueChange = { lateTolerance = it },
                            label = { Text("Toleransi Keterlambatan (Menit)") },
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(14.dp)
                        )
                    }
                }
            }

            item {
                Text(
                    text = "Otorisasi Reset Data Wajah Aparatur",
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Bold,
                    color = SoftBlack
                )
            }

            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(SurfaceWhite, shape = RoundedCornerShape(22.dp))
                        .border(1.dp, CardBorder, shape = RoundedCornerShape(22.dp))
                        .padding(20.dp)
                ) {
                    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        Text(
                            text = "Gunakan fitur ini apabila aparatur mengganti perangkat HP atau terjadi perubahan fisik yang memerlukan perekaman ulang wajah.",
                            fontSize = 12.sp,
                            color = TextMuted
                        )
                        Button(
                            onClick = { showSuccessSnackbar = true },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFEF4444)),
                            shape = RoundedCornerShape(14.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Text("Reset Data Biometrik Wajah", fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }

            item {
                Spacer(modifier = Modifier.height(20.dp))
            }
        }

        if (showSuccessSnackbar) {
            AlertDialog(
                onDismissRequest = { showSuccessSnackbar = false },
                confirmButton = {
                    Button(
                        onClick = { showSuccessSnackbar = false },
                        colors = ButtonDefaults.buttonColors(containerColor = EmeraldPrimary)
                    ) {
                        Text("OK")
                    }
                },
                title = { Text("Sukses", fontWeight = FontWeight.Bold, color = EmeraldPrimary) },
                text = { Text("Pengaturan konfigurasi kantor & presensi berhasil disimpan ke database.") },
                shape = RoundedCornerShape(20.dp)
            )
        }
    }
}
