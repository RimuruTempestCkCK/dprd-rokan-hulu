# 🏛️ Sistem Manajemen Rapat DPRD Kabupaten Rokan Hulu

## 📘 Deskripsi Proyek
**Sistem Manajemen Rapat DPRD Kabupaten Rokan Hulu** adalah aplikasi web berbasis **PHP** yang dirancang untuk mengelola jadwal rapat, agenda, dan informasi terkait **Dewan Perwakilan Rakyat Daerah (DPRD)** Kabupaten Rokan Hulu.

Sistem ini menyediakan:
- **Antarmuka publik** untuk menampilkan informasi DPRD dan jadwal rapat.
- **Dashboard khusus untuk admin dan anggota dewan (dewan)** untuk mengelola data rapat dengan mudah.
- **Fitur cetak undangan rapat dalam format PDF** menggunakan library **DOMPDF**.
- **Sistem autentikasi** agar akses lebih aman.

---

## ✨ Fitur Utama

### 🌐 Halaman Publik (`index.php`)
- **Informasi DPRD:** Profil, fungsi, tugas, dan kontak DPRD Kabupaten Rokan Hulu.  
- **Jadwal Rapat Publik:** Menampilkan rapat jenis *Terbuka* (judul, tanggal, tema, status, hasil).  
- **Cetak Undangan PDF:** Mencetak undangan rapat dengan template resmi DPRD (logo & kop surat).  
- **Navigasi Responsif:** Mendukung tampilan mobile dengan toggle menu.  
- **Form Kontak:** Mengirim pesan langsung ke DPRD.  
- **Statistik Dinamis:** Animasi counter untuk data seperti jumlah anggota dan komisi.  
- **Footer dan Media Sosial:** Informasi kontak resmi dan tautan sosial media.

---

### 🧑‍💼 Dashboard Admin
- **Statistik Rapat:** Total rapat, rapat berlangsung, selesai, dan terjadwal.  
- **Manajemen Rapat:** Tambah, edit, dan hapus rapat.  
- **Monitoring Real-time:** Pantau status rapat secara langsung.  
- **Keamanan Data:** Login khusus untuk admin.  
- **Waktu Real-time:** Menampilkan tanggal & waktu saat ini.  
- **Aksi Cepat:** Shortcut ke fitur utama seperti tambah rapat atau melihat website.

---

### 🧑‍💻 Dashboard Dewan
- **Jadwal Rapat:** Melihat jadwal dan detail rapat DPRD.  
- **Antarmuka Sederhana:** Fokus pada informasi rapat relevan.  
- **Autentikasi:** Akses hanya untuk pengguna dengan role *dewan*.

---

## 🔒 Fitur Tambahan
- **Sistem Login dan Logout** dengan peran berbeda (admin & dewan).  
- **Koneksi Database Aman** menggunakan **PDO (PHP Data Object)**.  
- **Desain Responsif:** Mendukung desktop dan mobile.  
- **Cetak PDF:** Template resmi DPRD dengan font 12pt dan logo.

---

## 🧰 Teknologi yang Digunakan
| Komponen | Teknologi |
|-----------|------------|
| **Bahasa Pemrograman** | PHP 7.x atau lebih tinggi |
| **Database** | MySQL (via PDO) |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+) |
| **Library Eksternal** | DOMPDF, Font Awesome, Google Fonts |
| **Framework CSS** | Custom responsive CSS |
| **Server** | Apache / Nginx dengan PHP |
| **Browser Support** | Chrome, Firefox, Safari, Edge |

---

## 🖥️ Persyaratan Sistem
- PHP 7.0 atau lebih tinggi  
- MySQL 5.7 atau lebih tinggi  
- Composer (untuk instalasi DOMPDF)  
- Apache/Nginx  
- Browser modern  

---

## ⚙️ Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/username/sistem-rapat-dprd-rohul.git
cd sistem-rapat-dprd-rohul
```

### 2. Instal Dependensi
Pastikan **Composer** sudah terinstal, lalu jalankan:
```bash
composer install
```

### 3. Konfigurasi Database
Buat database baru:
```sql
CREATE DATABASE dprd_rohul;
```

Kemudian buat tabel `rapat`:
```sql
CREATE TABLE rapat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    jenis ENUM('Terbuka', 'Tertutup') NOT NULL,
    tanggal DATETIME NOT NULL,
    isi TEXT,
    status ENUM('Terjadwal', 'Berlangsung', 'Selesai') DEFAULT 'Terjadwal',
    hasil TEXT
);
```

### 4. Konfigurasi Koneksi Database
Edit file `koneksi.php`:
```php
$host = 'localhost';
$db   = 'dprd_rohul';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$pdo  = new PDO($dsn, $user, $pass);
```

### 5. Upload File ke Server
- Upload ke direktori web server (misal: `/var/www/html/dprd-rohul`).
- Pastikan folder `img/` berisi file `logo.png`.

### 6. Set Permission
Pastikan folder untuk **cache DOMPDF** memiliki izin tulis:
```bash
chmod -R 775 vendor/dompdf
```

### 7. Akses Aplikasi
- Halaman publik: [http://localhost/dprd-rohul/index.php](http://localhost/dprd-rohul/index.php)  
- Dashboard: [http://localhost/dprd-rohul/login.php](http://localhost/dprd-rohul/login.php)

---

## 🚀 Penggunaan

### 👥 Untuk Pengguna Umum
- Akses halaman utama.
- Lihat jadwal rapat terbuka.
- Klik tombol **Cetak** untuk undangan PDF.

### 🛠️ Untuk Admin
- Login sebagai **admin**.  
- Kelola rapat melalui dashboard.  
- Tambah/edit/hapus data rapat dan hasil rapat.

### 🧑‍⚖️ Untuk Dewan
- Login sebagai **dewan**.  
- Lihat jadwal rapat dari dashboard dewan.

---

## 📁 Struktur Direktori
```bash
sistem-rapat-dprd-rohul/
├── index.php                 # Halaman utama publik
├── login.php                 # Halaman login
├── logout.php                # Logout
├── koneksi.php               # Koneksi database
├── style_index.css           # CSS halaman utama
├── style_dashboard.css       # CSS dashboard
├── script.js                 # JavaScript umum
├── img/
│   └── logo.png              # Logo DPRD
├── admin/
│   ├── dashboard.php         # Dashboard admin
│   └── kelola_rapat.php      # CRUD rapat
├── dewan/
│   ├── dashboard.php         # Dashboard dewan
│   └── jadwal_rapat.php      # Jadwal rapat
├── layout/
│   └── sidebar.php           # Sidebar dashboard
├── vendor/                   # Dependensi Composer (DOMPDF)
└── README.md                 # Dokumentasi proyek ini
```

---

