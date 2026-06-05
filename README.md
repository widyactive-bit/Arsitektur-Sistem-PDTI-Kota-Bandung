# PSTI Sport Analytics & Management System (PSAMS) Kota Bandung

PSAMS adalah sistem informasi analitik dan manajemen olahraga enterprise-grade yang dikembangkan khusus untuk **Persatuan Sepak Takraw Indonesia (PSTI) Kota Bandung**. Aplikasi ini menggabungkan web admin panel, mobile app (absensi GPS & profil prestasi), serta AI analytics untuk rekomendasi latihan berkala atlet.

---

## 🏗️ Arsitektur Folder Proyek
```
📂 arsitektur-sistem-psti-bandung
├── 📂 ai_service/           # FastAPI AI Analytics Service (Python & OpenAI API)
├── 📂 backend/              # Laravel 12 API & Filament Admin Panel (PHP 8.4/8.5)
├── 📂 docker/               # Konfigurasi Nginx Web Server & Database
├── 📂 mobile/               # Flutter Mobile App (Material 3)
├── 📄 docker-compose.yml    # Orkestrasi Docker container global
└── 📄 README.md             # Dokumen panduan instalasi ini
```

---

## 🛠️ Prasyarat Instalasi
Pastikan sistem Anda sudah terpasang:
- **Docker & Docker Compose** (Minimal Docker version 20+)
- **Git**
- **Flutter SDK** (jika ingin membuild & menjalankan aplikasi mobile secara lokal)
- **OpenAI API Key** (untuk fungsionalitas AI Analytics)

---

## 🚀 Langkah Instalasi & Uji Coba

### 1. Kloning Repositori & Persiapan
Buka terminal Anda, masuk ke direktori proyek:
```bash
cd "f:\Arsitektur Sistem PSTI Kota Bandung"
```

### 2. Konfigurasi Environment Backend & AI
Salin file environment dan atur kredensial:
- Di folder `/backend`:
  Buat berkas `.env` (sesuaikan konfigurasi database ke host: `db` di Docker network, port `3306`, user: `psams_user`, pass: `psams_password`).
- Di folder `/ai_service`:
  Buat berkas `.env` dan masukkan API Key OpenAI Anda:
  ```env
  OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx
  ```

### 3. Menjalankan Docker Containers
Jalankan orkestrasi Docker untuk menyalakan database, Redis, Laravel backend, dan FastAPI:
```bash
docker-compose up -d --build
```
Verifikasi bahwa semua kontainer berjalan:
```bash
docker ps
```

### 4. Setup Laravel Database & Seeds
Masuk ke container backend untuk menjalankan migrasi skema tabel serta mengisi data awal (seeds) pengguna dan parameter default:
```bash
docker exec -it psams_backend php artisan migrate --seed
```

---

## 🔑 Akun Uji Coba (Default Admin)
Gunakan kredensial berikut untuk masuk ke **Filament Admin Panel** via browser (`http://localhost`):
- **Email**: `superadmin@psti.bandung.go.id`
- **Kata Sandi**: `password123`
- **Role**: Super Admin

---

## 📡 Daftar Port Layanan (Localhost)
- **Web Admin Panel (Nginx/Laravel)**: `http://localhost` (Port 80)
- **AI Service (FastAPI Swagger)**: `http://localhost:8000/docs` (Port 8000)
- **MySQL Database Server**: `localhost:3306`
- **Redis Server**: `localhost:6379`
