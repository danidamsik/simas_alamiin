# Task: Build SIMAS Al-Amiin
> Baca file `prd.md` dan `db-seed.md` sebelum memulai. Seluruh keputusan implementasi harus mengacu pada kedua file tersebut.

---

## Phase 0 — Setup Dependency & Environment

### Task
- Install Laravel Breeze dengan Vue 3 dan Inertia.js
- Install library DomPDF (barryvdh/laravel-dompdf) untuk export laporan PDF
- Install library Maatwebsite Excel untuk export laporan Excel
- Pastikan Vite dan build asset frontend berjalan normal
- Pastikan integrasi Laravel + Vue 3 + Inertia.js + Tailwind CSS berjalan baik
- Pastikan halaman login dan dashboard default dari Breeze dapat diakses
- Verifikasi project dapat dijalankan tanpa error dasar

---

## Phase 1 — Migration & Model

### Task
- Analisis kebutuhan tabel berdasarkan file `prd.md` dan `db-seed.md`
- Buat migration untuk seluruh tabel yang dibutuhkan sistem sesuai struktur data di `prd.md` section Database Schema
- Pastikan semua foreign key dan constraint relasi antar tabel sudah terdefinisi dengan benar
- Kolom `nip` pada tabel guru bersifat nullable
- Tambahkan kolom `keterangan` (string, nullable) pada tabel `siswa_kelas_history` untuk mencatat alasan perpindahan kelas seperti "pindah kelas" atau "naik kelas"
- Tambahkan kolom `ip_address` (string, nullable) pada tabel `audit_log`
- Kolom `action` pada tabel `audit_log` adalah enum dengan nilai: create, update, delete
- Tambahkan unique constraint pada tabel absensi untuk kombinasi 4 kolom: tanggal + kelas_id + session_id + periode_id
- Tambahkan soft delete pada tabel siswa dan guru agar data historis tidak hilang
- Tambahkan database index pada kolom yang sering diquery sesuai ketentuan di `prd.md`: siswa.nis, users.email, absensi.tanggal, absensi.kelas_id, absensi.periode_id
- Buat semua Model beserta relasi antar model (belongsTo, hasMany, hasOne) sesuai relationship summary di `prd.md`
- Pastikan model Siswa dan Guru menggunakan SoftDeletes
- Jalankan migration

---

## Phase 2 — Seeder

### Task
- Buat seeder untuk seluruh data dummy sesuai yang tertera di file `db-seed.md`
- Ikuti urutan eksekusi seeder sesuai dependency order yang ditentukan di `db-seed.md`
- Seed data periode, kelas, user, guru, siswa, session, jadwal, dan absensi
- Untuk detail absensi dengan absensi_id = 1, seed persis sesuai data contoh di `db-seed.md`
- Untuk 39 absensi lainnya, generate detail absensi otomatis untuk semua siswa di kelas yang bersangkutan menggunakan distribusi bobot status sesuai ketentuan di `db-seed.md`
- Pastikan semua password user di-hash dengan bcrypt
- Daftarkan semua seeder di DatabaseSeeder dan jalankan

---

## Phase 3 — Authentication & Authorization

### Task
- Tambahkan kolom role pada tabel users dengan nilai yang diizinkan: admin, guru, kepala_sekolah
- Buat middleware untuk proteksi akses berdasarkan role
- Pastikan hanya admin yang dapat mengakses halaman manajemen data master, user, session, dan jadwal
- Pastikan guru hanya dapat mengakses data absensi miliknya sendiri sesuai jadwal mengajarnya
- Pastikan kepala sekolah hanya dapat melihat data absensi, melihat laporan, dan mengekspor laporan — tidak bisa input atau edit apapun
- Pastikan semua route terproteksi dengan middleware auth
- Auto redirect ke dashboard jika sudah login dan mencoba mengakses halaman login
- Aktifkan fitur lupa password bawaan Breeze: pengguna klik "Lupa Password" → input email → sistem kirim link reset ke email → pengguna klik link → input password baru
- Pastikan link reset password expired setelah 60 menit sesuai ketentuan di `prd.md`
- Tambahkan fitur reset password manual oleh admin melalui halaman manajemen user
- Terapkan pembatasan hak akses sesuai ketentuan di section Role & Permission pada `prd.md`

---

## Phase 4 — Layout & Navigasi

### Task
- Buat layout utama aplikasi dengan sidebar dan navbar
- Sidebar berisi navigasi utama yang menyesuaikan menu berdasarkan role pengguna yang sedang login
- Navbar menampilkan nama user yang sedang login dan tombol logout
- Buat komponen reusable yang akan digunakan di seluruh halaman: button, input, select, tabel, badge status, modal konfirmasi, dan pagination
- Badge status harus menampilkan warna berbeda untuk setiap status absensi: hadir (hijau), izin (kuning), sakit (biru), alfa (merah)
- Buat sistem toast notification global berbasis event yang dapat dipanggil dari mana saja setelah aksi CRUD berhasil atau gagal
- Pastikan desain mengikuti design system yang ditentukan di `prd.md`: warna primary hijau (#16A34A), font Inter/Poppins, rounded, shadow ringan
- Pastikan tampilan responsif

---

## Phase 5 — Manajemen Data Master (Admin)

### Task
- Buat halaman CRUD untuk data Periode
- Tambahkan fitur Set Aktif pada halaman periode — saat satu periode diaktifkan, periode lain otomatis nonaktif
- Buat halaman CRUD untuk data Kelas
- Buat halaman CRUD untuk data Siswa dengan fitur pencarian berdasarkan nama atau NIS dan filter berdasarkan kelas
- Pastikan siswa tidak bisa dihapus permanen, hanya dinonaktifkan (is_active = false)
- Tambahkan fitur pindah kelas siswa: saat admin mengubah kelas siswa, sistem otomatis mengupdate kelas_id di tabel siswa dan menyimpan riwayat kelas lama ke tabel siswa_kelas_history beserta keterangan perpindahan
- Buat halaman CRUD untuk data Guru
- Pastikan guru tidak bisa dihapus permanen, hanya dinonaktifkan
- Buat halaman CRUD untuk manajemen User beserta pengaturan role
- Tambahkan tombol reset password pada halaman manajemen user agar admin bisa mereset password pengguna secara manual
- Semua tabel data menggunakan pagination 25 baris per halaman

---

## Phase 6 — Manajemen Session & Jadwal (Admin)

### Task
- Buat halaman CRUD untuk Session (jam pelajaran)
- Terapkan validasi overlap waktu antar session di backend — jika waktu baru tumpang tindih dengan session yang sudah ada, tampilkan pesan error yang menyebutkan session mana yang konflik
- Buat halaman CRUD untuk Jadwal Pelajaran
- Jadwal harus terikat ke kelas, guru, session, periode, mata pelajaran, dan hari
- Terapkan validasi duplikasi jadwal: kelas yang sama tidak boleh punya jadwal di hari dan session yang sama, dan guru yang sama tidak boleh mengajar di hari dan session yang sama
- Tampilkan jadwal dalam format yang mudah dibaca, dikelompokkan per hari

---

## Phase 7 — Input Absensi (Guru & Admin)

### Task
- Buat halaman input absensi untuk guru
- Halaman input hanya menampilkan jadwal mengajar guru yang sedang login pada hari ini
- Guru memilih jadwal yang ingin diinputkan absensinya
- Sistem menampilkan daftar siswa aktif dari kelas yang bersangkutan
- Semua siswa secara default memiliki status hadir, guru hanya mengubah siswa yang tidak hadir
- Terapkan validasi di backend: absensi hanya bisa diinput sesuai jadwal guru, pada hari yang sesuai, dan dalam rentang waktu session berlangsung
- Cegah input duplikat dengan unique constraint 4 kolom: tanggal + kelas_id + session_id + periode_id
- Cegah input absensi jika tidak ada periode aktif
- Buat halaman daftar riwayat absensi dengan filter tanggal, kelas, dan periode, serta pagination

---

## Phase 8 — Edit Absensi & Audit Log

### Task
- Buat fitur edit absensi
- Untuk guru: edit hanya diizinkan pada hari yang sama dan selama session masih berlangsung ditambah toleransi 30 menit setelah jam selesai session. Jika batas waktu sudah lewat, tampilkan pesan "Waktu edit telah berakhir, hubungi admin" dan seluruh input dikunci
- Untuk admin: edit diizinkan kapan saja tanpa batasan waktu
- Setiap perubahan data absensi yang dilakukan oleh admin wajib dicatat ke tabel audit_log, menyimpan data sebelum (old_value) dan sesudah (new_value) perubahan, beserta informasi user_id, action (create/update/delete), nama model, model_id, ip_address, dan waktu

---

## Phase 9 — Dashboard

### Task
- Buat halaman dashboard dengan konten berbeda berdasarkan role pengguna
- Dashboard Admin dan Kepala Sekolah: tampilkan total siswa aktif, total kehadiran hari ini per status (Hadir/Izin/Sakit/Alfa), persentase kehadiran hari ini, grafik batang tren kehadiran 7 hari terakhir per status, dan tabel 5 kelas dengan tingkat kehadiran terendah hari ini
- Dashboard Guru: tampilkan jadwal mengajar hari ini (kelas, mata pelajaran, jam), status per kelas apakah sudah diinput atau belum, dan total hadir/tidak hadir untuk kelas yang sudah diinput
- Semua query dashboard wajib menggunakan Eager Loading untuk menghindari N+1 query

---

## Phase 10 — Halaman Absensi untuk Kepala Sekolah

### Task
- Buat halaman khusus bagi kepala sekolah untuk melihat data absensi secara detail
- Kepala sekolah dapat melihat data absensi per kelas, per tanggal, dan per siswa
- Sediakan filter berdasarkan periode, kelas, dan tanggal
- Kepala sekolah hanya bisa melihat data, tidak bisa input atau edit apapun
- Gunakan pagination 25 baris per halaman

---

## Phase 11 — Laporan Absensi

### Task
- Buat halaman laporan dengan form filter: periode (wajib), kelas (wajib), dan rentang tanggal (opsional, default seluruh periode)
- Tampilkan preview rekap data sebelum export
- Validasi: tidak boleh generate laporan jika data kosong atau filter wajib belum dipilih
- Buat fitur export PDF menggunakan DomPDF dengan format: header nama sekolah, periode, kelas, tanggal cetak — tabel rekap per siswa (No, Nama, NIS, Total Hadir, Izin, Sakit, Alfa, Persentase Kehadiran) — footer kolom tanda tangan kepala sekolah
- Buat fitur export Excel menggunakan Maatwebsite dengan format: freeze header row, warna baris selang-seling, baris total di bagian bawah
- Fitur laporan hanya dapat diakses oleh admin dan kepala sekolah

---

## Phase 12 — Final Check

### Task
- Jalankan migrate fresh dengan seed dan pastikan tidak ada error
- Verifikasi login dengan ketiga akun: admin, guru, dan kepala sekolah
- Pastikan setiap role hanya bisa mengakses menu dan fitur yang sesuai haknya
- Pastikan dashboard menampilkan data yang benar sesuai role
- Pastikan validasi overlap session berjalan dengan baik
- Pastikan batas waktu edit absensi untuk guru berjalan dengan benar
- Pastikan fitur pindah kelas siswa menyimpan riwayat ke tabel siswa_kelas_history dengan benar
- Pastikan audit log tercatat lengkap setiap kali admin mengubah data absensi
- Pastikan fitur reset password via email dan reset manual oleh admin berjalan
- Pastikan export PDF dan Excel berhasil diunduh dengan format yang sesuai
- Pastikan toast notification muncul setelah setiap aksi CRUD
- Pastikan tidak ada N+1 query pada halaman yang menampilkan data relasi
- Jalankan build frontend dan pastikan tidak ada error
