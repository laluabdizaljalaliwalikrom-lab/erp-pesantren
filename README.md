# ERP Pesantren Modern 🌙

Sistem Manajemen Terpadu untuk Pondok Pesantren yang mengedepankan efisiensi administrasi, transparansi keuangan, dan kemudahan akses bagi wali santri.

![ERP Pesantren Banner](https://raw.githubusercontent.com/laluabdizaljalaliwalikrom-lab/erp-pesantren/main/public/images/landing/hero.png)

## 🚀 Fitur Utama

### 1. Manajemen Santri & Dapodik Sync
*   **Smart Import Dapodik**: Bulk import data santri dari file Excel Dapodik dengan deteksi header otomatis dan pembersihan data.
*   **Validasi Anti-Duplikasi**: Sistem otomatis mengecek `NISN` untuk mencegah data ganda.
*   **Profil Lengkap**: Menyimpan data NIK, No. KK, riwayat sekolah, data alamat detail, hingga posisi anak dalam keluarga sesuai standar Dapodik.

### 2. Portal Wali Santri
*   **Self-Registration**: Wali santri dapat mendaftar secara mandiri.
*   **Dashboard Keuangan**: Memantau tagihan (SPP, Uang Makan, dll) secara real-time.
*   **Pembayaran Online**: Integrasi pembayaran yang memudahkan wali santri.

### 3. CMS Landing Page (Dynamic)
*   **Pengaturan Branding**: Ubah Logo, Favicon, dan Warna Tema (Primary Color) langsung dari Admin Panel.
*   **Manajemen Konten**: Edit Judul Hero, Visi & Misi, Sejarah, hingga Sambutan Pimpinan Pesantren tanpa menyentuh kode.
*   **Statistik Otomatis**: Menampilkan jumlah santri, pengajar, dan alumni secara dinamis.

### 4. Manajemen Keuangan & Akademik
*   **Billing System**: Pembuatan tagihan masal dan otomatis.
*   **Laporan Keuangan**: Rekapitulasi pembayaran santri yang akurat.

---

## 🛠️ Stack Teknologi

*   **Backend**: [Laravel 13](https://laravel.com)
*   **Admin Panel**: [Filament v4](https://filamentphp.com) (TALL Stack)
*   **Frontend**: [Tailwind CSS v4](https://tailwindcss.com) (Modern Utility-first)
*   **Database**: MySQL / PostgreSQL
*   **Asset Bundler**: Vite
*   **Excel Engine**: Spatie Simple Excel

---

## 💻 Instalasi Lokal

1. **Clone Repository**
   ```bash
   git clone https://github.com/laluabdizaljalaliwalikrom-lab/erp-pesantren.git
   cd erp-pesantren
   ```

2. **Instal Dependensi**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env`.*

4. **Migrasi & Seeder**
   ```bash
   php artisan migrate --seed
   ```

5. **Storage Link** (Penting untuk Gambar/Logo)
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Aplikasi**
   ```bash
   # Terminal 1 (Laravel Server)
   php artisan serve

   # Terminal 2 (Vite Dev Server)
   npm run dev
   ```

---

## 🐳 Docker Deployment

Aplikasi ini sudah dilengkapi dengan `Dockerfile` yang dioptimalkan untuk Cloud Run atau VPS.

```bash
docker build -t erp-pesantren .
docker run -p 8080:80 erp-pesantren
```

---

## 📁 Struktur Penting

*   `app/Filament/Resources`: Logika utama Admin Panel & Portal Wali.
*   `app/Models`: Definisi skema database (Student, AppSetting, dll).
*   `resources/views/landing.blade.php`: Template utama Landing Page.
*   `database/migrations`: Riwayat perubahan skema database.

---

## 📄 Lisensi

Distribusi terbatas untuk keperluan pengembangan internal Pesantren. Dibuat dengan ❤️ untuk kemajuan umat.

---
**Kontak Pengembang**: [Lalu Abdizal Jalaliwalikrom](https://github.com/laluabdizaljalaliwalikrom-lab)
