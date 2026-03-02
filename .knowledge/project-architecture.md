# Analisis Lengkap Project newdikerjaweb

Aplikasi **e-Kinerja / e-Presensi** berbasis **Laravel 10** untuk manajemen kepegawaian pemerintah daerah (kabupaten/kota). Mencakup pengelolaan pegawai, jabatan, kehadiran, sasaran kinerja (SKP), aktivitas harian, laporan TPP, dan profil pegawai.

---

## Arsitektur & Tech Stack

| Komponen | Detail |
|---|---|
| **Framework** | Laravel 10.x (PHP ^8.1) |
| **Auth** | Dual-guard session auth (Sanctum tersedia) |
| **DB** | MySQL (via Eloquent + raw DB query) |
| **PDF** | DomPDF + mPDF |
| **Excel** | PhpSpreadsheet |
| **Storage** | Flysystem FTP/SFTP |
| **ID** | UUID v4 (ramsey/uuid) |
| **Frontend** | Blade templates + Vite |

---

## Sistem Autentikasi (Dual-Guard)

### Guard Configuration (`config/auth.php`)
- **`web`** → Provider `users` → Model `App\Models\User`
- **`administrator`** → Provider `admin` → Model `App\Models\Admin`

### Login Flow (`AuthController`)
1. Attempt login via `Auth::attempt()` (web guard) dulu
2. Jika gagal, coba `Auth::guard('administrator')->attempt()`
3. Set session: `tahun_penganggaran`, `session_jabatan`, `session_nama`, `session_foto`, `session_satuan_kerja`, `session_tipe_pegawai`
4. Redirect berdasarkan role ke dashboard masing-masing

### Role System

**Administrator Guard (admin table):**
- Role 1 → Super Admin → `dashboard-super-admin`
- Role 2 → Admin Kabupaten → `dashboard-kabupaten`
- Role 3 → Admin Keuangan → `dashboard-keuangan`

**Web Guard (users table):**
- Role 1 → Pegawai → `dashboard-pegawai`
- Role 2 → Admin OPD → `dashboard-opd`
- Role 3 → Admin Unit → `dashboard-opd`

---

## Middleware (7 Custom)

| Alias | File | Guard | Roles yang Diizinkan |
|---|---|---|---|
| `authcheck` | `AuthCheck.php` | Both | Redirect jika sudah login |
| `admin_kabupaten` | `AdminKabupaten.php` | administrator | Role 2, 3 |
| `admin_keuangan` | `AdminKeuangan.php` | administrator | Role 3 |
| `users` | `Users.php` | web | Role 1, 2, 3 |
| `admin_opd` | `AdminOpd.php` | web | Role 1, 3 |
| `admin_unit` | `AdminUnit.php` | web | Role 1, 3 |
| `super_admin` | `SuperAdmin.php` | administrator | Role 1 |

---

## Struktur Routes (`routes/web.php` — 802 baris)

### Route Groups Utama

| Group | Middleware | Deskripsi |
|---|---|---|
| Public | — | Dashboard index, login, set-tahun |
| Admin Kabupaten | `admin_kabupaten` | Management level kabupaten: master data, pegawai, jabatan, laporan |
| Pegawai/User | `users` | Fitur pegawai: SKP, aktivitas, review, realisasi, profil |
| Admin OPD | `admin_opd`, `users`, `admin_unit` | Management OPD: pegawai-opd, jabatan-opd, laporan-opd |

### Modul per Group

**Admin Kabupaten:**
- `check-aktivitas`, `riwayat-aktivitas`
- `master-data` (agama, eselon, pendidikan, golongan, satuan)
- `pegawai` (list, verifikasi, masuk, keluar, pensiun, non-job, akan-pensiun)
- `user`, `admins`
- `perangkat-daerah` (perangkat-daerah, unit-kerja, lokasi)
- `master-jabatan` (jenis-jabatan, kelompok-aktivitas, master-jabatan)
- `hari-libur`, `versi-aplikasi`, `pengumuman`, `kehadiran`
- `jabatan` (list-jabatan, jabatan-kosong, jabatan-plt, mutasi)
- `layanan` (master-layanan, layanan-cuti, layanan-general)
- `laporan` (sasaran-kinerja, kehadiran, kinerja, tpp, profil, pegawai)

**Users/Pegawai:**
- `dashboard-pegawai`, `dashboard-pegawai-data`
- `sasaran-kinerja` (CRUD SKP)
- `aktivitas` (CRUD + calendar view)
- `review` (sasaran-kinerja, aktivitas, realisasi-skp)
- `realisasi` (update realisasi SKP)
- `akun` (change password)
- `layanan-pegawai` (layanan-cuti)
- `profil` (13 sub-sections: data-pribadi, riwayat-pendidikan-formal, non-formal, kepangkatan, jabatan, catatan-hukuman-dinas, diklat-struktural/fungsional/teknis, penghargaan, istri, anak, orang-tua, saudara, tambahan, file-pegawai)
- `laporan-pegawai` (sasaran-kinerja, kehadiran, kinerja, tpp)

**Admin OPD:**
- `pegawai-opd`, `aktivitas-opd`, `master-jabatan-opd`, `jabatan-opd`
- `kehadiran-opd`, `layanan-opd`, `akun-opd`, `laporan-opd`
- `perangkat-daerah-opd`

---

## Database Schema (Tabel Utama)

| Tabel | Model | Deskripsi |
|---|---|---|
| `users` | `User` | Akun pegawai (uuid, id_pegawai, role) |
| `admin` | `Admin` | Akun administrator |
| `tb_pegawai` | `Pegawai` | Data pegawai (NIP, nama, golongan, dll) |
| `tb_skp` | `SasaranKinerja` | Sasaran Kinerja Pegawai |
| `tb_aktivitas` | `Aktivitas` | Aktivitas harian pegawai |
| `tb_absen` | `Absen` | Data presensi/kehadiran |
| `tb_jabatan` | `Jabatan` | Penempatan jabatan pegawai |
| `tb_master_jabatan` | `MasterJabatan` | Master data jabatan |
| `tb_satuan_kerja` | — | SKPD / Satuan Kerja |
| `tb_unit_kerja` | `UnitKerja` | Unit kerja dalam SKPD |
| `tb_lokasi` | `Lokasi` | Lokasi absensi |
| `tb_libur` | `HariLibur` | Hari libur |
| `tb_golongan` | `Golongan` | Master golongan |
| `tb_eselon` | `Eselon` | Master eselon |
| `tb_pendidikan` | `Pendidikan` | Master pendidikan |
| `tb_agama` | `Agama` | Master agama |
| `tb_satuan` | `Satuan` | Master satuan kerja |
| `tb_jenis_jabatan` | `JenisJabatan` | Jenis jabatan |
| `tb_kelompok_jabatan` | — | Kelompok jabatan |
| `tb_master_aktivitas` | `MasterAktivitas` | Master aktivitas |
| `tb_aspek_skp` | `AspekSkp` | Aspek penilaian SKP |

### Model Profil (16 sub-model di `app/Models/profil/`)
Riwayat: pendidikan formal, non-formal, kepangkatan, jabatan, penghargaan, istri, anak, orang tua, saudara, bahasa, keahlian. Juga: catatan hukuman dinas, diklat (struktural/fungsional/teknis), file pegawai.

### Relasi Model Utama
- `SasaranKinerja` → hasMany `AspekSkp` (via `id_skp`)
- `SasaranKinerja` → hasMany `Aktivitas` (via `id_sasaran`)
- `Aktivitas` → belongsTo `SasaranKinerja` (via `id_sasaran`)
- `User` → linked ke `Pegawai` via `id_pegawai`
- `Pegawai` → linked ke `Jabatan` via `tb_jabatan.id_pegawai`
- `Jabatan` → linked ke `MasterJabatan` via `id_master_jabatan`
- `Jabatan` → linked ke `UnitKerja` via `id_unit_kerja`
- `Pegawai` → linked ke `SatuanKerja` via `id_satuan_kerja`

---

## Controllers (52 total)

### Controllers Utama (by size)

| Controller | Size | Fungsi |
|---|---|---|
| `ProfilController` | 106KB | Profil pegawai (13 sub-modul CRUD) |
| `LaporanSasaranKinerjaController` | 107KB | Export laporan SKP |
| `LaporanKehadiranController` | 54KB | Export laporan kehadiran |
| `LaporanTppController` | 54KB | Perhitungan & export TPP |
| `LaporanKinerjaController` | 35KB | Export laporan kinerja |
| `DashboardController` | 33KB | 5 dashboard + statistik |
| `ListJabatanControlller` | 35KB | CRUD jabatan + cetak |

### Controller Subdirectories

| Dir | Controllers | Fungsi |
|---|---|---|
| `jabatan/` | ListJabatan, JabatanKosong, JabatanPlt, Mutasi | Pengelolaan jabatan |
| `pegawai/` | listPegawai, verifikasi, masuk, keluar, pensiun, nonJob | Pengelolaan pegawai |
| `perangkat_daerah/` | PerangkatDaerah, UnitKerja, Lokasi | Struktur organisasi |
| `master_data/` | agama, eselon, golongan, pendidikan, satuan | Master data |
| `master_jabatan/` | JenisJabatan, MasterJabatan | Master jabatan |
| `master_aktivitas/` | KelompokAktivitas, MasterAktivitas | Master aktivitas |
| `layanan/` | MasterLayanan, LayananCuti, LayananGeneral | Layanan kepegawaian |
| `review/` | SasaranKinerjaReview, AktivitasReview, RealisasiReview | Review atasan |

---

## Traits

### `General` (`app/Traits/General.php` — 892 baris)
Fungsi utama:
- **Option helpers**: `option_golongan()`, `option_pendidikan()`, `option_eselon()`, `option_satuan_kerja()`, `option_unit_kerja()`, `option_agama()`, `option_satuan()`
- **Pegawai lookup**: `checkJabatanDefinitif()`, `findPegawai()`, `findAtasan()`, `infoSatuanKerja()`
- **Pensiun**: `option_akan_pensiun()` — hitung usia pensiun dari NIP (58/60 tahun berdasarkan jabatan)
- **Kehadiran**: `data_kehadiran_pegawai()` — perhitungan lengkap kehadiran, keterlambatan, potongan
- **SKP**: `optionSkp()`, `getMasterAktivitas()`

### `Presensi` (`app/Traits/Presensi.php` — 248 baris)
- `jumlahHariKerja($bulan)` — hitung hari kerja efektif
- `konvertWaktu()` — hitung selisih waktu masuk/pulang
- `konvertWaktuNakes()` — khusus tenaga kesehatan (shift pagi/siang/malam)
- `isRhamadan()` — cek apakah tanggal ada di bulan Ramadan
- `getDateRange()` — rentang tanggal tertentu

---

## Helpers (`app/helpers.php`)

| Fungsi | Deskripsi |
|---|---|
| `hasRole()` | Menentukan guard + role + id user yang sedang login |
| `konvertBulan($bulan)` | Konversi angka bulan ke nama Indonesia |
| `ManageFileDatatable($path)` | Kontrol akses datatable berdasarkan path |

---

## Tipe Pegawai & Aturan Kehadiran

| Tipe | Hari Kerja | Jam Masuk | Jam Pulang |
|---|---|---|---|
| `pegawai_administratif` | Sen-Jum | 07:30 (Ramadan: 08:00) | 16:00 (Jum: 15:30, Ramadan: 15:00) |
| `tenaga_pendidik` | Sen-Sab | 07:00/07:30 | 14:00 (Jum: 11:30) |
| `tenaga_pendidik_non_guru` | Sen-Sab | 07:00/07:30 | 14:00 (Jum: 11:30) |
| `tenaga_kesehatan` | Setiap hari (3/2 shift) | Per shift | Per shift |
| `tenaga_kesehatan_non_shift` | Sen-Sab | 07:00/07:30 | 14:00 (Jum: 11:30) |

---

## Perhitungan Potongan Kehadiran

```
Potongan Total = TK + KMK + CPK + Apel

TK  (Tanpa Keterangan) = jumlah_alfa × 3%
KMK (Keterlambatan Masuk Kerja):
  - 1-30 menit  = 0.5%
  - 31-60 menit = 1%
  - 61-90 menit = 1.25%
  - >90 menit   = 1.5%
CPK (Cepat Pulang Kerja): sama dengan KMK
Apel:
  - Tidak ikut apel Senin = 2%
  - Tidak ikut apel Sel-Jum = 0.25%
```

---

## Views Structure (Blade Templates)

```
resources/views/
├── layouts/          (layout.blade.php, header, footer, aside)
├── auth/             (login)
├── dashboard/        (5 view dashboard)
├── admin_kabupaten/  (11 subdirectory: master_data, pegawai, jabatan, dll)
├── pegawai/          (49 files - profil detail)
├── jabatan/          (9 files)
├── laporan/          (18 files - export templates)
├── review/           (8 files)
├── aktivitas/        (3 files)
├── sasaran_kinerja/  (2 files)
├── kehadiran/        (1 file)
└── akun/             (1 file)
```

---

## Form Requests (36 total)

Validasi terpisah untuk: Login, Pegawai (create/update), Absen, Aktivitas, SKP, Jabatan, Cuti, Mutasi, dan semua master data. Terletak di `app/Http/Requests/`.

---

## Pola Umum pada CRUD Controller

Setiap modul mengikuti pola konsisten:
1. `index()` → Return view
2. `datatable()` → Return JSON untuk DataTables
3. `store()` → Simpan data baru (dengan Form Request validation)
4. `show($params)` → Ambil data by UUID
5. `update($params)` → Update data by UUID
6. `delete($params)` → Hapus data by UUID
7. `option()` → Return data untuk dropdown/select (opsional)
8. `cetak()` / `export()` → Generate PDF/Excel (opsional)

---

## Session Variables

| Key | Deskripsi |
|---|---|
| `tahun_penganggaran` | Tahun anggaran aktif (default: tahun sekarang) |
| `session_jabatan` | Status jabatan: `definitif` atau `plt` |
| `session_jabatan_kode` | ID jabatan yang aktif |
| `session_nama` | Nama pegawai |
| `session_nama_jabatan` | Nama jabatan |
| `session_foto` | URL foto profil |
| `session_satuan_kerja` | Nama unit kerja |
| `session_tipe_pegawai` | Tipe pegawai (administratif, pendidik, dll) |
