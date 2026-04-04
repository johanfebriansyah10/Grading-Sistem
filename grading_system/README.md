# Sistem Penilaian Siswa dengan Analitik Prediktif

Aplikasi berbasis web ini dirancang untuk mengelola nilai siswa dan memberikan **analisis prediktif** terkait performa siswa. Sistem ini menggunakan **Regresi Linier Berganda** untuk memprediksi apakah seorang siswa masuk dalam kategori **"Aman"** atau **"Berisiko"** berdasarkan kombinasi nilai tugas, UTS, dan kehadiran mereka.

*Dibangun menggunakan: PHP Native, MySQL, & Bootstrap 5.*

---

## 📥 Cara Mengunduh (Download) dari GitHub

Jika Anda ingin mencoba menjalankan project ini di komputer Anda, Anda bisa mengunduhnya dari GitHub dengan 2 cara:

**Cara 1: Menggunakan ZIP (Paling Mudah)**
1. Di halaman GitHub repository ini, klik tombol berwarna hijau bertuliskan **Code**.
2. Pilih **Download ZIP**.
3. Ekstrak file ZIP yang sudah didownload ke dalam PC Anda.

**Cara 2: Menggunakan Git Clone (Untuk Developer)**
Buka Terminal / Command Prompt lalu jalankan:
```bash
git clone https://github.com/USERNAME-ANDA/NAMA-REPOSITORY.git
```

---

## 🚀 Panduan Instalasi (Cara Menjalankan Project)

Agar aplikasi dapat berjalan dengan lancar saat dicopy oleh orang lain, ikuti langkah-langkah instalasi berikut:

### 1. Persiapan Server Lokal
Pastikan komputer Anda sudah terinstal web server lokal, seperti **XAMPP**, **Laragon**, atau **WAMP**. 
- Jika memakai **XAMPP**: Pindahkan folder project ini ke dalam direktori `C:/xampp/htdocs/`.
- Jika memakai **Laragon**: Pindahkan folder project ini ke dalam direktori `C:/laragon/www/`.

### 2. Setup Konfigurasi Database
1. Buka aplikasi **XAMPP Control Panel** / **Laragon** dan nyalakan (Start) modul **Apache** dan **MySQL**.
2. Buka browser dan ketik: `http://localhost/phpmyadmin` (atau GUI MySQL bawaan Laragon seperti HeidiSQL).
3. Buat database baru dengan nama: `grading_system`.
4. Buka database tersebut, lalu pilih menu **Import**.
5. Cari dan unggah (upload) file `schema.sql` yang berada di dalam folder project ini, lalu klik **Go / Import**.

### 3. (Opsional) Cek File Konfigurasi
Secara default, aplikasi akan terkoneksi ke database tanpa password (standar XAMPP/Laragon). Jika MySQL Anda menggunakan password, buka file `config.php` dan sesuaikan baris ini:
```php
$user = 'root';
$pass = ''; // Isi dengan password mysql Anda jika ada
```

### 4. Jalankan Aplikasi
Buka browser (Google Chrome, Firefox, dll) dan akses URL berikut:
```text
http://localhost/NAMA_FOLDER_PROJECT_ANDA
```
*Contoh URL: `http://localhost/grading_system`*

---

## 🔑 Akun Default Tersedia

Untuk pertama kali login dan mencoba fitur, gunakan akses Admin bawaan:
- **Username:** `admin`
- **Password:** `admin123`

---

## ✨ Fitur Aplikasi
* **Manajemen Pengguna:** Akses eksklusif untuk Admin mengelola guru dan siswa.
* **Master Data:** Operasi CRUD (Tambah, Edit, Hapus) mulus menggunakan antarmuka modern.
* **Input Nilai:** Guru dapat memasukkan nilai (Tugas, UTS, Kehadiran).
* **Prediksi Kelulusan:** Sistem secara otomatis menjalankan rumusan KKM. Jika Y < 70, siswa terdeteksi sebagai "Berisiko".
* **Laporan:** Fitur cetak tabel prediksi siswa.
