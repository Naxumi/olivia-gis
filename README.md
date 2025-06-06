# EcoBarter: Geospatial-Driven Waste Revolution

**Submission untuk Lomba Web Technology OLIVIA X 2025 Universitas Brawijaya**

**Nama Tim:** Green Flag
**Perguruan Tinggi:** Universitas Brawijaya
**Tema:** Sustainable Digital Solutions: Enhancing Innovation through Web Technology

## Anggota Tim

* **Muhammad Hafizh Alfurqon** - 233140700111042 - (Peran dalam tim　： Backend Developer, GIS Specialist)
* **Faiz Henri Kurniawan** - 233140700111048 - (Peran dalam tim　: Frontend Developer, Prompt Consultant)
* **Talitha Rizqa Zayyanti** - 244140207111052 - (Peran dalam tim : Project Manager, Dokumentasi)

**Dosen Pembimbing:** Rachmad Andri Atmoko, S.ST, M.T

---

## 1. Deskripsi Aplikasi

**EcoBarter** adalah sebuah platform marketplace berbasis web dan Sistem Informasi Geografis (GIS) yang dirancang untuk merevolusi cara pengelolaan dan perdagangan limbah daur ulang di Indonesia. Dengan mempertemukan penjual limbah (pengepul atau individu) dengan pembeli (industri daur ulang atau pengrajin), EcoBarter menciptakan ekosistem ekonomi sirkular yang efisien, transparan, dan berkelanjutan.

Aplikasi ini tidak hanya memfasilitasi transaksi jual-beli, tetapi juga mengintegrasikan sistem logistik dengan pelacakan langsung (live tracking), memberikan insentif berupa poin ramah lingkungan (Eco Points), dan menyediakan data analitik sederhana untuk mendukung keberlanjutan.

## 2. Fitur Utama

EcoBarter dibangun dengan serangkaian fitur yang dirancang untuk menjawab tantangan dalam tema "Sustainable Digital Solutions":

* **Marketplace Limbah:** Pengguna dengan peran 'Seller' dapat membuka toko online, memajang jenis-jenis limbah yang siap jual beserta stok, harga, dan gambar.
* **Pencarian Geospasial (GIS):** Pengguna dapat mencari dan memfilter limbah berdasarkan berbagai kriteria, termasuk **lokasi terdekat** dari posisi mereka saat ini, berkat integrasi dengan PostGIS.
* **Sistem Transaksi Lengkap:** Alur transaksi yang jelas dari `pending`, `confirmed` oleh penjual, `picked_up` oleh kurir, hingga `delivered`, memastikan setiap langkah termonitor dengan baik.
* **Live Tracking Distributor:** Pembeli dan penjual dapat memantau posisi kurir (distributor) secara langsung di peta saat pengiriman berlangsung, lengkap dengan estimasi jarak dan waktu tiba yang dihitung menggunakan API rute eksternal.
* **Sistem Peran Pengguna:** Manajemen pengguna yang komprehensif dengan peran yang jelas: **Buyer**, **Seller**, **Distributor**, dan **Admin**, masing-masing dengan hak akses yang berbeda.
* **Eco Points System:** Mekanisme gamifikasi untuk memberikan penghargaan kepada pengguna yang menyelesaikan transaksi, mendorong partisipasi aktif dalam ekonomi sirkular.

## 3. Arsitektur dan Teknologi yang Digunakan

Aplikasi ini dibangun sebagai **Single Page Application (SPA)** dengan backend sebagai penyedia API, memastikan pengalaman pengguna yang cepat dan responsif.

* **Backend:**
    * **Framework:** Laravel 12
    * **Bahasa:** PHP 8.2
    * **Database:** PostgreSQL dengan ekstensi **PostGIS** untuk menangani data dan query geospasial.
    * **Autentikasi API:** Laravel Sanctum untuk autentikasi SPA yang aman.
    * **Paket Utama:**
        * `clickbar/laravel-magellan`: Toolbox modern untuk integrasi PostGIS di Laravel.
        * `spatie/laravel-permission`: Untuk manajemen peran dan izin.

* **Frontend:**
    * **Framework:** ajax, jquery, react
    * **Styling:** Tailwind CSS
    * **Pustaka Peta:** Leaflet.js 

* **Layanan Pihak Ketiga:**
    * **Peta Dasar:** OpenStreetMap (sesuai ketentuan lomba).
    * **API Rute:** OSRM (Open Source Routing Machine) Demo Server (untuk menghitung jarak dan durasi rute pengiriman).

## 4. Konfigurasi dan Deployment

* **Lingkungan Development:** XAMPP dengan PHP 8.2, PostgreSQL + PostGIS.
* **Hosting:** Microsoft Azure
* **Domain:** soon
* **Keamanan:** Aplikasi di-deploy dengan protokol HTTPS menggunakan sertifikat SSL/TLS untuk enkripsi data.

## 5. Instalasi dan Setup Proyek (Untuk Panitia)

Repository ini bersifat *private*. Panitia akan diundang sebagai kolaborator. Untuk menjalankan proyek secara lokal:

1.  **Clone Repository:**
    ```bash
    git clone https://www.andarepository.com/
    cd nama-proyek
    ```

2.  **Instal Dependensi:**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment:**
    * Salin file `.env.example` menjadi `.env`.
        ```bash
        cp .env.example .env
        ```
    * Jalankan `php artisan key:generate`.
    * Atur koneksi database PostgreSQL di file `.env`, pastikan user memiliki hak untuk membuat database baru.

4.  **Setup Database:**
    * Pastikan ekstensi **PostGIS** sudah diaktifkan di database PostgreSQL Anda. Anda bisa mengaktifkannya dengan perintah SQL: `CREATE EXTENSION IF NOT EXISTS postgis;`
    * Jalankan migrasi dan seeder untuk membuat tabel dan mengisi data awal.
        ```bash
        php artisan migrate:fresh --seed
        ```

5.  **Jalankan Aplikasi:**
    * Jalankan server development Laravel:
        ```bash
        php artisan serve
        ```
    * Compile aset frontend dan jalankan dalam mode watch:
        ```bash
        npm run dev
        ```

Aplikasi backend akan berjalan di `http://127.0.0.1:8000` dan frontend (tergantung setup Anda) mungkin berjalan di port lain.

---

Terima kasih atas kesempatan yang diberikan dalam kompetisi OLIVIA X 2025.
