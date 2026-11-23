# DOKUMEN ARSITEKTUR SISTEM - SIDELA
**Sistem Informasi Desa & Layanan Aparatur**  
**Pemerintah Desa Iraonogela, Kecamatan Mandrehe, Kabupaten Nias Barat**

---

## 1. Arsitektur Umum Sistem

SIDELA dibangun menggunakan pola arsitektur **Clean Architecture** dan **MVVM (Model-View-ViewModel)** pada sisi klien mobile, serta arsitektur **Layered Modular Service** pada backend Kotlin Ktor.

```
+-------------------------------------------------------------------+
|                     ANDROID MOBILE CLIENT                         |
|  - Jetpack Compose UI (Material 3)                                |
|  - ViewModels & StateFlow (UDF - Unidirectional Data Flow)        |
|  - Repositories & Local DataStore Preferences                     |
|  - CameraX + Google ML Kit Face Detection & Liveness Engine       |
|  - FusedLocationProvider (GPS Geofencing Client)                  |
|  - Retrofit 2 + OkHttp 3 (JWT Bearer Interceptor)                 |
+---------------------------------+---------------------------------+
                                  | HTTPS / JSON REST API
+---------------------------------v---------------------------------+
|                    BACKEND REST API (KOTLIN KTOR)                 |
|  - Netty Engine & Route Controllers                               |
|  - JWT Authentication & Role-Based Access Control (RBAC)          |
|  - Services Layer (Attendance, Face, Leave, Journal, Reports)     |
|  - Mathematical GeoLocation Engine (Haversine Formula)            |
|  - Biometric Embedding Comparator (Cosine Similarity 192-dim)     |
|  - Exposed ORM & HikariCP Connection Pool                         |
+---------------------------------+---------------------------------+
                                  | JDBC / TCP:3306
+---------------------------------v---------------------------------+
|                      MYSQL 8.0 DATABASE                           |
|  - 21 Normalized Relational Tables (InnoDB, UTF8mb4)              |
|  - Soft Delete Pattern, Foreign Keys, Indexes, Audit Logs        |
+-------------------------------------------------------------------+
```

---

## 2. Algoritma Geofencing (Rumus Haversine)

Untuk memvalidasi bahwa presensi benar-benar dilakukan di Kantor Pemerintah Desa Iraonogela tanpa bergantung pada GPS palsu (Mock Location), sistem backend menghitung jarak lengkung lingkaran bumi (*great-circle distance*) menggunakan rumus Haversine:

$$\Delta\text{lat} = \text{lat}_2 - \text{lat}_1$$
$$\Delta\text{lon} = \text{lon}_2 - \text{lon}_1$$
$$a = \sin^2\left(\frac{\Delta\text{lat}}{2}\right) + \cos(\text{lat}_1) \cdot \cos(\text{lat}_2) \cdot \sin^2\left(\frac{\Delta\text{lon}}{2}\right)$$
$$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
$$d = R \cdot c$$

Dimana:
- $R = 6,371,000 \text{ meter}$ (Jari-jari rata-rata bumi).
- Koordinat Kantor Desa Iraonogela: $\text{Lat} = 1.0425000^\circ$, $\text{Lon} = 97.5258000^\circ$.
- Toleransi radius yang diizinkan admin: $50 \text{ meter}$.

---

## 3. Algoritma Face Recognition & Liveness Detection

Sistem mencegah manipulasi presensi menggunakan foto statis melalui pipeline 2 tahap:

1. **Liveness Detection di Perangkat (ML Kit On-Device)**:
   - Memeriksa sudut rotasi wajah (*head Euler angles*) untuk memastikan wajah tegak 3D di depan kamera.
   - Menguji probabilitas kedipan mata (*Eye Blink State Machine*): mendeteksi transisi mata terpejam ($<0.25$) diikuti mata terbuka ($>0.70$).
   - Skor liveness dihitung secara dinamis; jika di bawah $0.65$, presensi otomatis dibatalkan.

2. **Verifikasi Kemiripan Identitas Wajah (Cosine Similarity)**:
   Vektor embedding wajah $\vec{A}$ (saat presensi) dibandingkan dengan vektor terdaftar $\vec{B}$ pada profil biometrik:

   $$\text{Similarity}(\vec{A}, \vec{B}) = \frac{\sum_{i=1}^{n} A_i \cdot B_i}{\sqrt{\sum_{i=1}^{n} A_i^2} \cdot \sqrt{\sum_{i=1}^{n} B_i^2}}$$

   Jika skor kemiripan $\ge 0.72$ (setara dengan $\ge 95\%$ tingkat kepercayaan), verifikasi identitas dinyatakan VALID.
