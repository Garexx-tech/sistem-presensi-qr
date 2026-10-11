# 🚀 SiPena - Sistem Presensi QR & WhatsApp Notification Gateway

Aplikasi presensi multi-platform modern berbasis web dan microservice yang dirancang fleksibel untuk instansi pendidikan (sekolah & perguruan tinggi). Sistem ini mengintegrasikan presensi berbasis QR Code secara real-time dengan layanan notifikasi otomatis WhatsApp melalui Fonnte Gateway API.

---

## 👥  Kelompok 2(B-PG)

Anggota:
1.Haydar Yahya Jamal(240602040)
2.Muhammad Faidlur Rizqi(240602042)
3.Muhammad Tegar Ramdhani(240602045)
4.Riyan Hidayat(240602065)
---

## 🏗️ Arsitektur & Teknologi

Sistem dibangun menggunakan pendekatan **Microservices Architecture** berbasis containerization:

* **Core Presensi Service:** Laravel 8.2 (PHP 8.2) — Mengelola logika utama, database, dan antarmuka web.
* **Notification Service:** Node.js / Express — Mengelola pengiriman notifikasi WhatsApp secara asinkron.
* **WhatsApp Gateway:** Fonnte API Integration.
* **Database:** MySQL 8.0.
* **Containerization:** Docker & Docker Compose.

---

## 🎯 Prinsip HCI / UX (User-Centric Design)

1. **Fleksibilitas Multi-Mode Instansi:** Mendukung peralihan istilah adaptif antara mode **Sekolah** (Siswa/Guru & ortu) dan mode **Perguruan Tinggi** (Mahasiswa/Dosen).
2. **Real-Time Visual Feedback:** Menampilkan indikator status pengiriman notifikasi WhatsApp (Terkirim, Antrean, Gagal) serta toast notification interaktif.
3. **Simplisitas Navigasi:** Tata letak *dashboard* yang bersih berbasis *color-coded metrics* dan grafik interaktif untuk mempermudah analisis data kehadiran.

---

## 🛠️ Cara Menjalankan Aplikasi (Instalasi via Docker)

### Prasyarat:
* Git
* Docker Desktop
* Laragon / XAMPP (MySQL)

### Langkah Setup:

1. **Clone Repository:**
   ```bash
   git clone [https://github.com/Garexx-tech/sistem-presensi-qr.git](https://github.com/Garexx-tech/sistem-presensi-qr.git)
   cd sistem-presensi-qr