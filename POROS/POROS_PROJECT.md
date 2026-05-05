# POROS — School Food Supply Chain Management System

> **Dokumen ini menjelaskan arsitektur, fitur, dan konvensi proyek POROS secara lengkap untuk konteks prompting AI di masa depan.**

---

## 1. Gambaran Umum

**POROS** (Program Optimalisasi Rantai pangan Operasional Sekolah) adalah sistem manajemen rantai pasok makanan sekolah berbasis web. Sistem ini mengelola siklus penuh mulai dari perencanaan menu, pengadaan bahan baku, produksi harian, pengiriman ke sekolah-sekolah, hingga monitoring gizi siswa dan food waste.

### Tujuan Utama
- Merencanakan menu makanan bergizi berdasarkan Tabel Komposisi Pangan Indonesia (TKPI)
- Mengelola inventory bahan baku dan supplier
- Menjadwalkan produksi harian dan pengiriman ke sekolah
- Memantau status gizi siswa melalui data antropometri (berat & tinggi badan)
- Meminimalkan food waste (plate waste monitoring)
- Memberikan dashboard analytics untuk pengambilan keputusan

---

## 2. Tech Stack

| Komponen | Teknologi |
|---|---|
| **Framework** | Laravel 11 (PHP) |
| **Database** | MySQL (port 3307, database: `ppl`) |
| **Frontend** | Blade templates + Vanilla CSS + Vanilla JS |
| **Typography** | Google Fonts — Inter (300–700) |
| **Charts** | Chart.js (CDN) |
| **Auth** | Laravel built-in session-based auth |
| **Session** | Database driver (`sessions` table) |
| **Design System** | Custom CSS dengan orange primary (#ff6b00) |

### Tidak Menggunakan
- Tidak menggunakan Vite/Webpack untuk CSS (CSS langsung di `public/css/`)
- Tidak menggunakan Tailwind CSS
- Tidak menggunakan JavaScript framework (React/Vue)
- Tidak menggunakan API — semua server-side rendered via Blade

---

## 3. Struktur Role & Akses

Sistem memiliki **3 role** yang disimpan di tabel `roles`:

| Role | Nama di DB | Menu Sidebar | Dashboard |
|---|---|---|---|
| **Super Admin** | `super admin` | Dashboard, Users Management, Suppliers & Bidding, Analytics, System Settings | Shared dashboard (stats cards, charts) |
| **Petugas Dapur** | `dapur` | Dashboard, Meal Planning, Inventory Management, Logistics & Deliveries | Shared dashboard |
| **Petugas Sekolah** | `sekolah` | Dashboard, School Monitoring | Shared dashboard |

### Akun Default (dari UserSeeder)
| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@poros.com` | `password123` |
| Petugas Dapur | `dapur@poros.com` | `password123` |
| Petugas Sekolah | `sekolah@poros.com` | `password123` |

### Mekanisme Auth
- Login via `AuthController` → email + password
- Setelah login, redirect otomatis berdasarkan role ke dashboard masing-masing
- Middleware `role:nama_role` (custom `RoleMiddleware`) melindungi route per role
- Logout menghapus session

---

## 4. Database Schema

### Entity Relationship

```
roles ──< users >── sekolahs
                       │
                       ├──< siswas >──< antropometris
                       │
                       └──< plate_wastes

suppliers ──< bahan_bakus ──< reseps >── menus ──< produksi_harians

suppliers ──< form_hargas
suppliers ──< stok_logs

sekolahs ──< pengirimans
menus ──< produksi_harians ──< pengirimans

kurirs ──< pengirimans

sekolahs ──< laporan_masalahs
```

### Tabel Utama & Field

#### `roles`
- `id`, `nama_role` (super admin / dapur / sekolah), timestamps, softDeletes

#### `users`
- `id`, `nama_lengkap`, `email` (unique), `password`, `role_id` (FK→roles), `sekolah_id` (nullable FK→sekolahs), `no_telp`, `lokasi`, `last_login_at`, `status`, timestamps, softDeletes

#### `sekolahs`
- `id`, `nama_sekolah`, `alamat`, `jumlah_siswa`, timestamps, softDeletes

#### `siswas`
- `id`, `nama_siswa`, `nisn`, `kelas`, `alergi`, `sekolah_id` (FK), `contact`, `status` (Active/Inactive), timestamps, softDeletes

#### `antropometris`
- `id`, `berat_badan`, `tinggi_badan`, `tanggal_ukur`, `siswa_id` (FK), timestamps, softDeletes

#### `suppliers`
- `id`, `nama_supplier`, `alamat`, `kontak`, timestamps, softDeletes

#### `katalog_pangans` (Master Data TKPI)
- `id`, `kode_bahan`, `nama_bahan`, `sumber_data`
- `energi_per_100g`, `protein_per_100g`, `karbohidrat_per_100g`, `lemak_per_100g`, `serat_per_100g`, `kalsium_per_100g`, `besi_per_100g`
- Berisi 1.066 bahan makanan standar Indonesia.
- timestamps, softDeletes

#### `bahan_bakus` (Bahan Baku / Raw Ingredients Inventory)
- `id`, `nama_bahan`, `satuan`, `stok` (dalam gram), `stok_minimal`, `supplier_id` (FK), `katalog_pangan_id` (FK)
- **Logika Gizi**: Data nutrisi tidak lagi di-hardcode di tabel ini, melainkan direferensikan dari tabel `katalog_pangans`.
- Memiliki accessors: `energi_per_gram`, `protein_per_gram`, `serat_per_gram`, `kalsium_per_gram`, `besi_per_gram`, dll.
- timestamps, softDeletes

#### `menus`
- `id`, `nama_menu`, `total_kalori`, `total_protein`, `total_karbohidrat`, `total_lemak`, `deskripsi_gizi`
- Nutrisi dihitung otomatis dari resep (sum of bahan × gramasi / 100)
- timestamps, softDeletes

#### `reseps` (Recipe — pivot antara menu dan bahan_baku)
- `id`, `menu_id` (FK), `bahan_id` (FK→bahan_bakus), `gramasi_per_porsi`
- timestamps, softDeletes

#### `produksi_harians` (Daily Production Schedule)
- `id`, `tanggal_produksi`, `total_target_porsi`, `status_produksi` (Menunggu/Proses/Selesai), `menu_id` (FK)
- timestamps, softDeletes

#### `plate_wastes`
- `id`, `jumlah_waste`, `tanggal`, `keterangan`, `sekolah_id` (FK), `pengiriman_id` (FK)
- timestamps, softDeletes

#### `pengirimans` (Deliveries)
- `id`, `tanggal_pengiriman`, `status_pengiriman`, `catatan`, `kurir_id` (FK), `sekolah_id` (FK), `produksi_harian_id` (FK)
- timestamps, softDeletes

#### `kurirs`
- `id`, `nama_kurir`, `no_hp`, `kendaraan`, timestamps, softDeletes

#### `form_hargas` (Price Forms)
- `id`, `harga_per_satuan`, `tanggal`, `supplier_id` (FK), `bahan_baku_id` (FK)
- timestamps, softDeletes

#### `stok_logs`
- `id`, `jenis_log` (masuk/keluar), `jumlah`, `tanggal`, `bahan_baku_id` (FK), `supplier_id` (FK nullable)
- timestamps, softDeletes

#### `laporan_masalahs` (Problem Reports)
- `id`, `judul`, `deskripsi`, `status`, `tanggal_laporan`, `sekolah_id` (FK), `user_id` (FK)
- timestamps, softDeletes

---

## 5. Fitur yang Sudah Diimplementasi

### ✅ Authentication
- Login/logout dengan session-based auth
- Role-based redirect setelah login
- Edit profile (nama & password)

### ✅ Dashboard (Shared — semua role)
- 4 stat cards: Total Students, Today's Deliveries, Stock Status, Food Waste
- Line chart: Nutrition Distribution Trends (Chart.js)
- Doughnut chart: Delivery Status breakdown
- Data dari database real (Siswa, BahanBaku, PlateWaste)

### ✅ User Management (Super Admin)
- Halaman terbagi dalam 2 **Tab**: System Users & Students
- **System Users**: Menampilkan pengguna sistem (Admin, Dapur, Petugas Sekolah) beserta role dan lokasi.
- **Students**: Menampilkan integrasi data Siswa dari tabel `siswas` lengkap dengan informasi kelas, NISN, alergi, dan sekolah mitra.
- Stats cards per role.
- Fungsi **Add New User/Student** pintar berdasarkan tab yang aktif via modal form.
- Aksi modern menggunakan Ikon (View, Edit, Hapus) menggunakan modal konfirmasi (AJAX-like visual, traditional POST/PUT/DELETE routing).
- View Student memunculkan modal yang juga meload **Data Fisik (Terbaru)** dari relasi `antropometris`.
- Menggunakan Bootstrap 5 Pagination Custom Styles (Tailwind default di-override via AppServiceProvider).

### ✅ Meal Planning & TKPI Integration (Dapur)
- Integrasi Penuh **Tabel Komposisi Pangan Indonesia (TKPI)**: 1.066 data master pangan lokal.
- Kalender menu mingguan dengan navigasi antar minggu.
- Add/Edit/Delete menu schedule per hari.
- Menu Library dengan **26 pre-built dynamic menus**.
- Kalkulasi nutrisi makro & mikro otomatis (Energi, Protein, Karbo, Lemak, Serat, Kalsium, Zat Besi).
- Searchable dropdown untuk pemilihan bahan baku.
- **Smart View Modal**: Menampilkan detail jadwal (kebutuhan bahan & info gizi) dengan logika *conditional rendering* (hanya menampilkan gizi yang nilainya > 0).
- Portion preview (total berat berdasarkan jumlah porsi).

### ✅ School Monitoring (Sekolah)
- Placeholder page (dalam pengembangan)

### 🚧 Placeholder Pages (Belum Diimplementasi)
- Suppliers & Bidding
- Analytics
- System Settings
- Inventory Management
- Logistics & Deliveries

---

## 6. Konvensi Kode

### Naming Conventions
- **Tabel DB**: snake_case plural Indonesia (`bahan_bakus`, `produksi_harians`, `plate_wastes`)
- **Model**: PascalCase singular (`BahanBaku`, `ProduksiHarian`, `PlateWaste`)
- **Controller**: PascalCase + Controller suffix (`MenuController`, `UserController`)
- **View**: kebab-case (`meal-planning.blade.php`) atau snake_case
- **Route name**: dot notation (`dashboard.superadmin`, `users.index`, `schedule.store`)
- **CSS class**: kebab-case (`stat-card`, `nav-link`, `menu-lib-grid`)

### Layout System
- `layouts/app.blade.php` — master layout (HTML head, font, CSS links, toast, notification scripts)
- `partials/sidebar.blade.php` — sidebar navigation (role-aware)
- `partials/header.blade.php` — top header dengan user profile dropdown
- Setiap halaman dashboard menggunakan pattern:
  ```blade
  @extends('layouts.app')
  @section('styles') ... @endsection
  @section('content')
  <div class="dashboard-layout">
      @include('partials.sidebar')
      <main class="main-content">
          @include('partials.header')
          <!-- Page content -->
      </main>
  </div>
  @endsection
  @section('scripts') ... @endsection
  ```

### CSS Architecture
- `public/css/global.css` — Modal alert styles, shared CSS variables
- `public/css/dashboard.css` — Layout, sidebar, header, cards, forms, buttons, toasts
- `public/css/users.css` — Users management specific styles (tabs, tables, avatars, pagination styling)
- `public/css/auth.css` — Login page styles
- `public/js/dashboard.js` — Sidebar toggle, profile dropdown, modal/toast notification system

### Design System
- **Primary color**: `#ff6b00` (orange)
- **Primary dark**: `#e66000`
- **Background**: `#f8f9fa`
- **Text dark**: `#0c1e35`
- **Text muted**: `#6b7280`
- **Border**: `#e5e7eb`
- **Font**: Inter (Google Fonts)
- **Border radius**: 12–20px (rounded modern)
- **Cards**: White background, subtle border, box-shadow

### Database Seeders (Run Order)
1. `RoleSeeder` — 3 roles
2. `SekolahSeeder` — sample schools
3. `SiswaSeeder` — sample students
4. `AntropometriSeeder` — sample anthropometric data
5. `UserSeeder` — 3 default users (1 per role)
6. `SupplierSeeder` — sample suppliers
7. `KatalogPanganSeeder` — import 1.066 data gizi dari CSV lokal TKPI
8. `BahanBakuSeeder` — 70+ bahan baku inventaris yang di-mapping ke Katalog Pangan
9. `MenuSeeder` — generate 26 menu dan otomatis meracik 100+ resep
10. `WasteSeeder` — sample plate waste data

---

## 7. Setup Lokal

```bash
# 1. Clone & masuk ke direktori
cd POROS/POROS

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=ppl
DB_USERNAME=root
DB_PASSWORD=

# 5. Jalankan migrasi & seeder
php artisan migrate:fresh --seed

# 6. Jalankan server
php artisan serve
```

---

## 7. Menjalankan E2E Testing (Laravel Dusk)

Ikuti langkah ini untuk menjalankan pengujian otomatis secara aman di database lokal:

1. **Siapkan Database Testing** (Hanya satu kali):
   Pastikan file database kosong ada di folder database:
   ```powershell
   touch database/testing.sqlite
   ```

2. **Update ChromeDriver** (Jika Chrome baru saja update):
   ```bash
   php artisan dusk:chrome-driver
   ```

3. **Jalankan Server Testing** (Buka di terminal terpisah):
   Server harus berjalan di port yang sama dengan `APP_URL` di `.env.dusk.local` (Default: 8001):
   ```bash
   php artisan serve --port=8001 --env=dusk.local
   ```
   *Catatan: Jika Anda menggunakan port lain (misal 8081), pastikan APP_URL di .env.dusk.local juga diubah.*

4. **Jalankan Pengujian**:
   Buka terminal baru, lalu pilih salah satu perintah:
   ```bash
   # Jalankan SEMUA test
   php artisan dusk

   # Jalankan test per fitur (contoh: Meal Planning)
   php artisan dusk tests/Browser/Dapur/MealPlanning/MealPlanningTest.php
   ```

---

## 8. Catatan Penting untuk AI Prompting

1. **Bahasa**: Kode menggunakan campuran Bahasa Indonesia (nama field DB, teks UI) dan Bahasa Inggris (nama class, method, CSS class)
2. **Semua model menggunakan SoftDeletes** — jangan lupa `softDeletes()` di migration dan `use SoftDeletes` di model
3. **Nutrisi dihitung per 100g** — Nilai gizi asli berada di tabel `katalog_pangans`. Model `BahanBaku` menggunakan **Accessor** (`getEnergiPerGramAttribute`) untuk membagi nilainya menjadi per gram. Kalkulasi akhir: `value_per_gram × gramasi_per_porsi`.
4. **Session driver = database** — tabel `sessions` harus ada
5. **CSS inline di blade** — saat ini beberapa blade file masih memiliki `<style>` inline (sedang dalam proses refactor untuk dipisahkan ke file CSS terpisah)
6. **Chart.js via CDN** — `https://cdn.jsdelivr.net/npm/chart.js`, diload di halaman yang membutuhkan chart
7. **Middleware role** — terdaftar di `bootstrap/app.php` sebagai alias `role`, menggunakan class `App\Http\Middleware\RoleMiddleware`
8. **Tidak ada API endpoint** — semua interaksi melalui form submission (POST/PUT/DELETE) dan Blade rendering
9. **Stok dalam gram** — field `stok` dan `stok_minimal` di `bahan_bakus` menggunakan satuan gram. Display: >= 1000g ditampilkan sebagai kg
10. **Pagination** — Diatur menggunakan `Paginator::useBootstrapFive()` di `AppServiceProvider` agar layout HTML mudah di-*style* dengan Vanilla CSS tanpa bentrok dengan struktur Tailwind default.
11. **Arsitektur Antropometri & Alergi** — `antropometris` DIBIARKAN terpisah dari `siswas` untuk *historical tracking* (mencegah stunting/chart pertumbuhan). Data Alergi diinput oleh Petugas Sekolah karena mereka berinteraksi langsung dengan siswa; Dapur hanya bersifat *viewer*.
12. **UI Conditional Rendering (Nol)** — Pada pop-up / modal, nilai nutrisi yang bernilai 0 tidak di-render (di-filter via JS) untuk menjaga UI tetap bersih.
13. **E2E Testing (Laravel Dusk)** — Framework pengujian otomatis menggunakan Laravel Dusk + Pest. Database testing menggunakan **SQLite** (`database/testing.sqlite`) via `.env.dusk.local`. Struktur folder: `tests/Browser/{Role}/{Feature}`. Gunakan `DatabaseMigrations` trait untuk memastikan state database bersih.
14. **Cross-Database Compatibility** — Seeder dan query harus mendukung PostgreSQL (Prod) dan SQLite (Test). Gunakan `DB::getDriverName() === 'pgsql' ? 'ilike' : 'like'` untuk pencarian string case-insensitive.
