# PANDUAN DEPLOYMENT & INSTALASI PROYEK SIDELA
**Sistem Informasi Desa dan Layanan Aparatur**  
**Pemerintah Desa Iraonogela, Kecamatan Mandrehe, Kabupaten Nias Barat**

---

## 1. Prasyarat Lingkungan (Prerequisites)

- **Sistem Operasi**: Windows 10/11, macOS, atau Ubuntu 20.04/22.04 LTS
- **Java Development Kit (JDK)**: OpenJDK 17 LTS (direkomendasikan Eclipse Temurin 17)
- **Database**: MySQL 8.0+ atau MariaDB 10.5+ (bisa melalui Docker)
- **Android Studio**: Android Studio Iguana / Koala / Hedgehog dengan Android SDK 34 (API 34)
- **Alat Bantu**: Git, Gradle 8.2+, Postman / Insomnia

---

## 2. Instalasi & Konfigurasi Database MySQL

### Opsi A: Menggunakan Docker Compose (Instan & Otomatis)
Buka terminal pada folder `sidela-database`:
```bash
cd sidela-database
docker compose up -d
```
Container MySQL 8.0 akan aktif pada port `3306`, dan phpMyAdmin aktif pada port `8081` (`http://localhost:8081`). Seluruh tabel dan data awal Desa Iraonogela akan ter-seed otomatis.

### Opsi B: Menggunakan MySQL Lokal (XAMPP / Laragon / Standalone MySQL)
1. Buka MySQL Client / phpMyAdmin / DBeaver / Navicat.
2. Buat database baru bernama `sidela_db`:
   ```sql
   CREATE DATABASE sidela_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Eksekusi file DDL:
   ```bash
   mysql -u root -p sidela_db < sidela-database/schema.sql
   ```
4. Eksekusi data awal (seed data):
   ```bash
   mysql -u root -p sidela_db < sidela-database/seeds.sql
   ```

---

## 3. Konfigurasi & Menjalankan Backend Ktor (Kotlin JVM)

1. Masuk ke direktori backend:
   ```bash
   cd sidela-backend
   ```
2. Sesuaikan konfigurasi database pada file `src/main/resources/application.conf`:
   ```hocon
   database {
       url = "jdbc:mysql://localhost:3306/sidela_db?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=Asia/Jakarta&characterEncoding=UTF-8"
       user = "root"
       password = "your_password"
   }
   ```
3. Jalankan server lokal:
   ```bash
   ./gradlew run
   ```
   *(Pada Windows PowerShell gunakan `.\gradlew.bat run`)*
4. Server Ktor akan aktif pada `http://localhost:8080`.
   Uji endpoint health:
   ```bash
   curl http://localhost:8080/api/v1/health
   ```

### Build Fat JAR untuk Server Produksi (Linux VPS)
```bash
./gradlew build
# Hasil file JAR berada di: build/libs/sidela-backend-1.0.0-all.jar
java -jar build/libs/sidela-backend-1.0.0-all.jar
```

---

## 4. Konfigurasi & Build Aplikasi Android Studio

1. Buka **Android Studio**.
2. Pilih **Open an Existing Project** dan arahkan ke direktori `sidela-android`.
3. Tunggu proses **Gradle Sync** selesai secara otomatis.
4. Sesuaikan `BASE_URL` pada file `app/build.gradle.kts`:
   - Jika menggunakan **Android Emulator**: `http://10.0.2.2:8080/`
   - Jika menggunakan **Perangkat Fisik HP**: Gunakan IP Wi-Fi lokal laptop server (contoh: `http://192.168.1.15:8080/`) atau domain HTTPS server produksi.
5. Jalankan aplikasi ke emulator atau HP:
   - Pilih tombol **Run 'app'** (Shift + F10).
6. Untuk menghasilkan file APK yang siap dipasang di HP aparatur:
   ```bash
   ./gradlew assembleDebug
   # File APK tersimpan di: app/build/outputs/apk/debug/app-debug.apk
   ```

---

## 5. Kredensial Akun Default Pemerintahan Desa Iraonogela

Seluruh akun default menggunakan kata sandi: `Password123!`

| Role / Jabatan | Username | Password | Deskripsi Akses |
|---|---|---|---|
| **Administrator Desa** | `admin` | `Password123!` | Akses penuh pengaturan radius GPS kantor, jam kerja, reset biometrik |
| **Kepala Desa** | `kades` | `Password123!` | Persetujuan Cuti, Izin, Sakit, verifikasi jurnal, dan monitoring presensi |
| **Sekretaris Desa** | `sekdes` | `Password123!` | Verifikasi administrasi, persetujuan surat masuk/keluar, data penduduk |
| **Kaur TU & Umum** | `rachel` | `Password123!` | Akun sesuai gambar referensi (Rachel Kim) untuk presensi masuk/pulang & jurnal |
| **Kaur Keuangan** | `kaurkeu` | `Password123!` | Pengelolaan penatausahaan kas desa dan inventaris barang |
| **Kepala Dusun I** | `kadus1` | `Password123!` | Pelaporan kegiatan kewilayahan Dusun Hilimbowo |
