# SPESIFIKASI API RESTFUL - SIDELA (KOTLIN KTOR)
**Sistem Informasi Desa dan Layanan Aparatur**  
**Pemerintah Desa Iraonogela, Kecamatan Mandrehe, Kabupaten Nias Barat**

Base URL: `http://localhost:8080/api/v1` (atau `http://10.0.2.2:8080/api/v1` pada Android Emulator)  
Format Data: `application/json`  
Autentikasi: `Authorization: Bearer <JWT_TOKEN>`

---

## 1. Modul Autentikasi & Akun

### 1.1 Login Pengguna
- **Endpoint**: `POST /auth/login`
- **Akses**: Publik
- **Request Body**:
```json
{
  "username": "rachel",
  "password": "Password123!"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Login berhasil. Selamat datang di SIDELA Iraonogela.",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "tokenType": "Bearer",
    "expiresInSeconds": 2592000,
    "user": {
      "id": 6,
      "username": "rachel",
      "email": "rachel.kim@iraonogela.desa.id",
      "fullName": "Rachel Kim",
      "nik": "1225012601950006",
      "phone": "081260000006",
      "avatarUrl": "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400",
      "role": {
        "id": 4,
        "name": "Perangkat Desa",
        "slug": "perangkat_desa"
      },
      "perangkatDesa": {
        "id": 6,
        "nipd": "199501262020012006",
        "jabatan": "Kaur TU & Umum",
        "dusunId": 2,
        "dusunName": "Dusun II Ononamolo",
        "statusKeaktifan": "AKTIF"
      },
      "isFaceEnrolled": true
    }
  }
}
```

### 1.2 Profil Pengguna Saat Ini
- **Endpoint**: `GET /auth/me`
- **Akses**: Authenticated (Bearer Token)
- **Response (200 OK)**: Detail profil lengkap aparatur yang sedang login.

---

## 2. Modul Presensi & Face Recognition

### 2.1 Ringkasan Presensi Hari Ini
- **Endpoint**: `GET /attendance/today`
- **Akses**: Authenticated
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Status presensi hari ini berhasil diambil.",
  "data": {
    "date": "2026-01-26",
    "dayName": "Monday",
    "clockIn": "--",
    "clockOut": "--",
    "workHours": "--",
    "breakTime": "--",
    "statusToday": "BELUM_HADIR",
    "isClockInAllowed": true,
    "isClockOutAllowed": false,
    "serverTime": "08:25",
    "officeRadiusMeters": 50,
    "officeLatitude": 1.0425000,
    "officeLongitude": 97.5258000
  }
}
```

### 2.2 Presensi Masuk (Clock In)
- **Endpoint**: `POST /attendance/clock-in`
- **Akses**: Authenticated
- **Request Body**:
```json
{
  "latitude": 1.0425010,
  "longitude": 97.5258020,
  "faceEmbedding": [0.045, -0.012, 0.089, ...],
  "livenessScore": 0.98,
  "photoBase64": "/9j/4AAQSkZJRg...",
  "notes": "Presensi masuk tepat waktu"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Presensi masuk berhasil dicatat.",
  "data": {
    "id": 105,
    "userName": "Rachel Kim",
    "userJabatan": "Kaur TU & Umum",
    "date": "2026-01-26",
    "clockIn": "08:25",
    "clockOut": "--",
    "workHours": "--",
    "status": "TERLAMBAT",
    "lateMinutes": 25,
    "distanceMeters": 12.5,
    "isWithinOfficeRadius": true,
    "faceConfidence": 98.5,
    "livenessPassed": true
  }
}
```

### 2.3 Presensi Pulang (Clock Out)
- **Endpoint**: `POST /attendance/clock-out`
- **Akses**: Authenticated
- **Request Body**: Sama dengan Clock In dengan koordinat dan vektor wajah saat pulang.

---

## 3. Modul Cuti, Izin, Sakit, & Dinas Luar

### 3.1 Buat Pengajuan Baru
- **Endpoint**: `POST /leaves`
- **Akses**: Authenticated
- **Request Body**:
```json
{
  "leaveType": "CUTI_TAHUNAN",
  "startDate": "2026-02-10",
  "endDate": "2026-02-12",
  "reason": "Keperluan keluarga",
  "attachmentUrl": "/uploads/doc_sk_cuti.pdf"
}
```

### 3.2 Persetujuan Pengajuan (Kepala Desa)
- **Endpoint**: `PUT /leaves/{id}/approve`
- **Akses**: Kepala Desa / Sekretaris Desa
- **Request Body**:
```json
{
  "action": "APPROVE",
  "notes": "Disetujui, silakan koordinasikan tugas harian kepada Sekdes."
}
```

---

## 4. Modul Jurnal Kerja Harian

### 4.1 Tambah Aktivitas Jurnal
- **Endpoint**: `POST /journals`
- **Akses**: Authenticated
- **Request Body**:
```json
{
  "date": "2026-01-26",
  "title": "New Year Brand Campaign Meeting",
  "description": "Koordinasi publikasi program pembangunan desa tahun 2026",
  "startTime": "10:00",
  "endTime": "11:00",
  "workOutput": "Notulensi rapat dan timeline publikasi",
  "status": "BACKLOG"
}
```

---

## 5. Modul Laporan & Ekspor

### 5.1 Ekspor PDF Resmi
- **Endpoint**: `POST /reports/attendance/export/pdf`
- **Request Body**:
```json
{
  "startDate": "2026-01-01",
  "endDate": "2026-01-31"
}
```
- **Response**: URL unduh dokumen PDF siap cetak dengan Kop Resmi Pemerintah Desa Iraonogela.
