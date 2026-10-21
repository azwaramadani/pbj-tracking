# Tracking Application Pengadaan Barang dan Jasa (PBJ)

Project Laravel untuk tugas PBL Kelompok — Sistem Tracking Pengadaan Barang dan Jasa.

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18.x (untuk asset frontend)

---

## Cara Instalasi

### 1. Clone repository

`ash
git clone <url-repository>
cd pbj-tracking
`

### 2. Install dependency PHP

`ash
composer install
`

### 3. Install dependency JavaScript (opsional, jika menggunakan asset frontend)

`ash
npm install
`

---

## Konfigurasi

### 4. Salin file .env

`ash
cp .env.example .env
`

> Di Windows (Command Prompt):
> `cmd
> copy .env.example .env
> `

### 5. Generate application key

`ash
php artisan key:generate
`

### 6. Atur konfigurasi database

Buka file .env dan sesuaikan konfigurasi database:

`env
DB_CONNECTION=sqlite
`

> SQLite sudah dikonfigurasi secara default. Tidak diperlukan setup tambahan untuk development.

### 7. Jalankan migrasi database

`ash
php artisan migrate
`

---

## Menjalankan Server Development

`ash
php artisan serve
`

Aplikasi akan berjalan di: **http://localhost:8000**

---

## Perintah Berguna

| Perintah | Keterangan |
|----------|------------|
| php artisan serve | Jalankan server development |
| php artisan migrate | Jalankan migrasi database |
| php artisan migrate:fresh | Reset dan jalankan ulang semua migrasi |
| php artisan make:model NamaModel | Buat model baru |
| php artisan make:controller NamaController | Buat controller baru |
| php artisan make:migration nama_migrasi | Buat file migrasi baru |
| php artisan route:list | Tampilkan semua route yang terdaftar |
| php artisan tinker | Buka Laravel REPL |

---

## Struktur Folder Utama

`
pbj-tracking/
├── app/
│   ├── Http/
│   │   └── Controllers/    # Controller aplikasi
│   ├── Models/             # Model Eloquent
│   └── Providers/          # Service provider
├── bootstrap/              # Bootstrap aplikasi
├── config/                 # Konfigurasi aplikasi
├── database/
│   ├── factories/          # Factory untuk testing
│   ├── migrations/         # File migrasi database
│   └── seeders/            # Seeder database
├── public/                 # Entry point & asset publik
├── resources/
│   ├── css/                # File CSS
│   ├── js/                 # File JavaScript
│   └── views/              # Template Blade
├── routes/
│   └── web.php             # Definisi route web
├── storage/                # File log & cache
├── tests/                  # Unit & Feature test
├── .env.example            # Contoh konfigurasi environment
├── artisan                 # CLI Laravel
├── composer.json           # Dependency PHP
└── package.json            # Dependency JavaScript
`

---

## URL Website

|Page |	URL	| Route name| 
|----------|------------|------------|
|Dashboard | /unit/dashboard | unit.dashboard |
|Daftar Permohonan | /unit/permohonan |unit.permohonan.index |
|Buat Permohonan |	/unit/permohonan/buat | unit.permohonan.create |
|Detail Permohonan | /unit/permohonan/{id} |unit.permohonan.show |
|Tracking |	/unit/tracking/{id?} | unit.tracking.index |
|Dokumen | /unit/dokumen | unit.dokumen.index |

## Kontribusi (untuk Anggota Kelompok)

1. Selalu buat branch baru sebelum mengerjakan fitur: git checkout -b fitur/nama-fitur
2. Jangan commit file .env (sudah ada di .gitignore)
3. Selalu jalankan composer install setelah pull dari repository
4. Pastikan php artisan migrate dijalankan setelah ada migrasi baru

---

## Lisensi

Project ini dibuat untuk keperluan tugas PBL dan tidak untuk dipublikasikan secara komersial.
