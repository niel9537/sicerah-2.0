# SIDELA — SISTEM INFORMASI DESA & LAYANAN APARATUR
### Pemerintah Desa Iraonogela, Kecamatan Mandrehe, Kabupaten Nias Barat

Aplikasi profesional terpadu untuk tata kelola pemerintahan desa, presensi aparatur berbasis **Face Recognition** dan **Liveness Detection**, **GPS Geofencing**, pencatatan **Jurnal Kerja Harian**, alur persetujuan **Cuti / Izin / Sakit / Dinas Luar**, serta pengelolaan data kependudukan dan aset desa.

---

## 📱 Desain UI/UX Mengikuti Referensi Desain Modern

| Halaman | Deskripsi Tampilan |
|---|---|
| **1. Dashboard (Today's Summary)** | Sapaan pengguna (*"Monday, 26 January / Welcome, Rachel Kim"*), foto profil avatar, bel notifikasi, kartu ringkasan *"Clock In -- / Clock Out --"* dengan tombol hijau *"Clock In Now"*, kartu statistik *"25 Request, 8 Approved, 16 Decline"*, serta kartu tugas *"New Year Brand Campaign Meeting"*, *"Meet New Client"*, dsb. Dilengkapi floating bottom navigation pill. |
| **2. Presensi (Attendance)** | Tombol bulat besar berwarna hijau emerald di tengah layar (*Big Clock In Button*) dengan glowing ripple shadow dan ikon tangan touch, tampilan jam digital besar (*"08.25"*), subteks *"Your location must be near in office"*, kartu metrik jam kerja, serta tabel log riwayat presensi (*Fri 23 Jan, Thu 22 Jan, Wed 21 Jan, Tue 20 Jan*). |
| **3. Face Recognition Scanner** | Kamera fullscreen dengan bingkai bracket sudut pemindaian wajah berteknologi tinggi, animasi garis laser pemindai vertikal, teks instruksi *"Posisikan wajah Anda / Kedipkan mata"*, serta tombol rana bulat-kotak hijau di bagian bawah. |

---

## 🛠️ Arsitektur & Teknologi

1. **Android Client**:
   - Bahasa: **Kotlin Native**
   - UI Toolkit: **Jetpack Compose** dengan **Material 3**
   - Pola Arsitektur: **Clean Architecture + MVVM + Repository Pattern**
   - Dependency Injection: **Dagger Hilt**
   - Kamera & Biometrik: **CameraX** + **Google ML Kit Face Detection** (Deteksi kedipan mata & liveness anti-spoofing)
   - Lokasi: **Google Play Services Location** (Geofencing GPS radius kantor desa)
   - Jaringan: **Retrofit 2** + **OkHttp 3** (JWT Interceptor)
   - Penyimpanan Lokal: **Jetpack DataStore Preferences**

2. **Backend REST API**:
   - Framework: **Kotlin Ktor 2.3+** (Asynchronous High-Performance Engine Netty)
   - Database ORM: **JetBrains Exposed ORM** + **HikariCP Connection Pool**
   - Keamanan: **JWT Authentication**, enkripsi sandi **BCrypt**, **StatusPages**, dan **CORS**
   - Matematika Spasial: **Rumus Haversine** untuk validasi radius GPS kantor desa
   - Algoritma Biometrik: **Cosine Similarity** vektor wajah 192-dimensi

3. **Database**:
   - Engine: **MySQL 8.0 / MariaDB**
   - 21 Tabel Normal: `users`, `roles`, `perangkat_desa`, `face_profiles`, `attendance`, `attendance_settings`, `leave_requests`, `official_trips`, `work_journals`, `journal_attachments`, `village_profiles`, `residents`, `families`, `hamlets`, `village_assets`, `incoming_letters`, `outgoing_letters`, `notifications`, `holidays`, `approvals`, `audit_logs`.
   - Data Awal (Seeds) terintegrasi untuk seluruh jajaran aparatur Desa Iraonogela.

---

## 📁 Struktur Direktori Proyek

```
SIDELA/
├── sidela-android/                # Proyek Android Studio (Kotlin Native + Jetpack Compose)
│   ├── app/src/main/
│   │   ├── java/id/desa/sidela/
│   │   │   ├── data/              # Model, Remote API Service, Repositories, DataStore
│   │   │   ├── di/                # Dagger Hilt Modules
│   │   │   ├── ui/
│   │   │   │   ├── components/    # BigClockInButton, FaceScanningOverlay, TaskCard, BottomNav
│   │   │   │   ├── navigation/    # SidelaNavGraph & Routes
│   │   │   │   ├── screens/       # Dashboard, Attendance, FaceScan, Journal, Leave, Admin
│   │   │   │   └── theme/         # Color, Shape, Type, Theme (Material 3 Emerald Theme)
│   │   │   └── utils/             # FaceAnalyzer, FaceRecognitionHelper, LocationHelper
│   │   └── AndroidManifest.xml
│   └── build.gradle.kts
│
├── sidela-backend/                # Proyek Backend REST API (Kotlin Ktor + Exposed + MySQL)
│   ├── src/main/kotlin/id/desa/sidela/
│   │   ├── database/              # DatabaseFactory, Exposed Tables
│   │   ├── models/                # DTOs & ApiResponse
│   │   ├── plugins/               # Routing, Security (JWT), Serialization, CORS
│   │   ├── routes/                # Auth, Attendance, Face, Leave, Journal, Village, Reports
│   │   ├── services/              # AttendanceService, FaceRecognitionService, JournalService
│   │   └── utils/                 # JwtConfig, PasswordHasher, GeoLocationUtil
│   └── src/main/resources/application.conf
│
├── sidela-database/               # File Database MySQL & Docker
│   ├── schema.sql                 # DDL 21 Tabel Relasional
│   ├── seeds.sql                  # Seed data lengkap Pemerintah Desa Iraonogela
│   └── docker-compose.yml         # Container MySQL 8.0 & phpMyAdmin
│
└── sidela-docs/                   # Dokumentasi Lengkap
    ├── api/API_SPECIFICATION.md   # Spesifikasi Endpoint REST API
    ├── deployment/DEPLOYMENT_GUIDE.md # Panduan Instalasi & Deployment
    └── architecture/ARCHITECTURE_DESIGN.md
```

---

## 🚀 Panduan Cepat Menjalankan Sistem

### 1. Jalankan Database MySQL
```bash
cd sidela-database
docker compose up -d
```

### 2. Jalankan Backend Ktor API
```bash
cd sidela-backend
./gradlew run
```
*API aktif pada:* `http://localhost:8080/api/v1/health`

### 3. Jalankan Aplikasi Android
1. Buka folder `sidela-android` di **Android Studio**.
2. Hubungkan HP Android atau jalankan Android Emulator.
3. Klik tombol **Run 'app'** atau build file APK dengan perintah:
   ```bash
   ./gradlew assembleDebug
   ```

---

## 🔑 Kredensial Login Default

*Kata sandi untuk seluruh akun default:* `Password123!`

- **Rachel Kim (Kaur TU & Umum - Sesuai Mockup):** `rachel`
- **Kepala Desa (Baziduhu Gulo):** `kades`
- **Sekretaris Desa (Faigizatulo Waruwu):** `sekdes`
- **Administrator Sistem:** `admin`
