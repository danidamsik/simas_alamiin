## 1. Project Overview

**Nama Project:**
SIMAS Al-Amiin

**Tagline:**
Membangun Disiplin Melalui Presensi Digital

**Deskripsi:**
SIMAS Al-Amiin (Sistem Informasi Manajemen Absensi) adalah aplikasi berbasis web yang dirancang untuk membantu sekolah dalam mengelola data kehadiran siswa. Sistem ini menggunakan metode presensi manual, di mana guru atau admin melakukan input data kehadiran siswa ke dalam sistem. Dengan adanya sistem ini, proses absensi yang sebelumnya dilakukan secara manual di buku dapat terdigitalisasi, sehingga data menjadi lebih aman, mudah diakses, serta dapat diolah menjadi laporan secara cepat dan akurat.

**Tujuan:**
Meningkatkan efisiensi dan kerapihan dalam pencatatan serta pengelolaan data kehadiran siswa melalui sistem digital berbasis input manual.

tujuan tambahan :
1. Mempermudah proses pencatatan absensi
Membantu guru dalam menginput kehadiran siswa dengan lebih cepat dibandingkan metode tulis manual di buku.
2. Mengurangi kesalahan pencatatan
Meminimalisir kesalahan seperti data ganda, tulisan tidak terbaca, atau kehilangan data.
3. Menyediakan data yang terstruktur
Data absensi tersimpan dengan rapi dan terorganisir sehingga mudah dicari dan dikelola.
4. Mempermudah pembuatan laporan
Memungkinkan pembuatan laporan kehadiran siswa secara otomatis (harian, bulanan, dll).
5. Meningkatkan kedisiplinan siswa
Dengan data yang tercatat dengan baik, sekolah dapat lebih mudah memantau tingkat kehadiran siswa.
6. Mendukung pengambilan keputusan
Data absensi dapat digunakan oleh pihak sekolah untuk evaluasi dan kebijakan.

**Target Pengguna:**
- Admin (Operator Sekolah)
- Guru
- Kepala Sekolah

### Scope
1. Dashboard 
Ringkasan data kehadiran
Statistik sederhana (jumlah hadir, tidak hadir, dll)
2. Manajemen Data Master
Data siswa
Data guru
Data kelas
Tahun ajaran
3. Manajemen Pengguna
Login & logout
Hak akses (admin, guru, kepala sekolah)
4. Pencatatan Absensi (Manual)
Input kehadiran siswa oleh guru
Status kehadiran (hadir, izin, sakit, alfa)
Edit & update data absensi
5. Pengelolaan Data Absensi
Penyimpanan data absensi
Pencarian data absensi
Filter berdasarkan tanggal, kelas, atau siswa
6. Laporan Absensi
Laporan harian
Laporan bulanan
Rekap kehadiran siswa

**Out of Scope:**
1. Absensi Otomatis
Tidak menggunakan QR Code
Tidak menggunakan fingerprint
Tidak menggunakan face recognition
2. Integrasi Eksternal
Tidak terhubung dengan sistem lain (misalnya sistem akademik lain)
Tidak ada integrasi API pihak ketiga
3. Notifikasi Otomatis
Tidak ada notifikasi ke orang tua (SMS/WhatsApp/email)
4. Tracking Lokasi / GPS
Tidak mendeteksi lokasi siswa atau guru
5. Aplikasi Mobile Native
Sistem hanya berbasis web
Tidak ada aplikasi Android/iOS khusus
6. Pembayaran / Keuangan
Tidak mencakup sistem pembayaran sekolah

---

## 2. Problem & Solution

**Problem:**
1. Pencatatan Absensi Masih Manual di Buku
Data ditulis tangan oleh guru
Rentan rusak, hilang, atau tercecer
2. Data Tidak Terstruktur
Sulit mencari data kehadiran siswa tertentu
Tidak ada pengelompokan yang rapi
3. Proses Rekap Memakan Waktu
Guru harus menghitung kehadiran secara manual
Laporan bulanan/semester lama dibuat
4. Tingkat Kesalahan Tinggi
Tulisan tidak terbaca
Salah input atau data terlewat
5. Sulit Monitoring oleh Pihak Sekolah
Kepala sekolah harus melihat buku satu per satu
Tidak ada ringkasan data yang cepat
6. Tidak Ada Riwayat Data Jangka Panjang yang Aman
Data lama bisa hilang atau rusak
Tidak ada backup

**Solution:**
1. Input Absensi Digital Berbasis Web
Guru mencatat kehadiran siswa langsung ke dalam sistem melalui antarmuka web yang mudah digunakan.
Data tersimpan secara digital di database, aman dari risiko rusak, hilang, atau tercecer.

2. Penyimpanan Data Terstruktur dan Mudah Dicari
Data absensi disimpan dengan relasi yang jelas: per siswa, per kelas, per sesi, dan per periode.
Tersedia fitur pencarian dan filter berdasarkan tanggal, kelas, siswa, dan periode sehingga data mudah ditemukan kapan saja.

3. Laporan Otomatis
Sistem menghasilkan laporan kehadiran secara otomatis (harian, bulanan, per periode) tanpa perlu menghitung manual.
Laporan dapat langsung dicetak atau diekspor ke format PDF/Excel.

4. Validasi Otomatis untuk Mencegah Kesalahan Input
Sistem mencegah input ganda (absensi dobel untuk kelas dan sesi yang sama).
Status absensi dipilih melalui dropdown (Hadir, Izin, Sakit, Alfa) sehingga tidak ada risiko tulisan tidak terbaca atau data terlewat.
Default status "Hadir" untuk semua siswa mempercepat proses input dan mengurangi kemungkinan lupa mengisi.

5. Dashboard Monitoring Terpusat
Kepala sekolah dapat memantau rekapitulasi kehadiran seluruh kelas langsung dari dashboard tanpa harus memeriksa buku satu per satu.
Dashboard menampilkan statistik kehadiran, ketidakhadiran, serta tingkat kedisiplinan siswa secara real-time berdasarkan periode aktif.

6. Data Tersimpan Aman di Database Jangka Panjang
Seluruh riwayat absensi tersimpan di database MySQL yang dapat dikelola dan dipelihara secara berkelanjutan.
Data diorganisir per periode sehingga rekap antar semester dan tahun ajaran tetap terjaga dan mudah diakses kembali.

---

## 3. Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | Vue 3, Inertia.js, Tailwind CSS |
| **Backend** | Laravel |
| **Database** | MySQL |

---

## 4. Core Features
1. Sistem Autentikasi (Login)
Deskripsi:
Fitur keamanan yang mengharuskan pengguna untuk login sebelum mengakses sistem, sehingga data lebih terjaga dan hanya bisa diakses oleh pihak yang berwenang.

2. Dashboard Monitoring
Deskripsi:
Fitur yang menampilkan ringkasan data absensi dalam bentuk statistik, berdasarkan periode aktif.

Metrik yang Ditampilkan:
Admin & Kepala Sekolah:
Total siswa aktif seluruh sekolah
Total kehadiran hari ini (Hadir / Izin / Sakit / Alfa) lintas semua kelas
Persentase kehadiran keseluruhan hari ini
Grafik batang: tren kehadiran 7 hari terakhir (per status)
Tabel ringkas: 5 kelas dengan kehadiran terendah
Guru:
Jadwal mengajar hari ini (kelas, mapel, jam)
Status absensi per kelas: sudah diinput / belum diinput
Total siswa hadir/tidak hadir untuk kelas yang sudah diinput hari ini

3. Manajemen Data Master
Deskripsi:
Fitur untuk mengelola data utama yang digunakan dalam sistem, seperti data siswa, guru, kelas, dan periode tahun ajaran. Data ini menjadi dasar dalam proses absensi.

4. Manajemen Pengguna & Hak Akses
Deskripsi:
Fitur untuk mengelola akun pengguna (admin, guru, kepala sekolah) beserta hak aksesnya, sehingga setiap pengguna hanya dapat mengakses fitur sesuai perannya.

5. Manajemen Jadwal & Jam Pelajaran (Session)
Deskripsi:
Fitur yang memungkinkan admin untuk mengatur jam pelajaran (session) dan jadwal mengajar, termasuk penentuan hari, kelas, mata pelajaran, guru, serta waktu mulai dan selesai. Jadwal mencakup informasi mata pelajaran agar absensi dapat diidentifikasi per mapel, bukan hanya per sesi waktu. Fitur ini digunakan untuk membatasi proses absensi agar hanya dapat dilakukan sesuai dengan jadwal yang telah ditentukan.

6. Input Absensi Manual
Deskripsi:
Fitur utama yang memungkinkan guru untuk mencatat kehadiran siswa secara manual ke dalam sistem dengan status seperti hadir, izin, sakit, atau alfa.

7. Pengelolaan Data Absensi
Deskripsi:
Fitur untuk melihat, mengedit, dan mengelola data absensi yang telah diinput, termasuk pencarian dan filter berdasarkan tanggal, kelas, siswa, dan periode.

8. Laporan Absensi
Deskripsi:
Fitur untuk menghasilkan laporan kehadiran siswa secara otomatis dalam bentuk harian, bulanan, atau per periode, sehingga memudahkan evaluasi.

Format Laporan:
PDF: menggunakan library DomPDF/Barryvdh. Laporan memuat header nama sekolah, periode, kelas, tanggal cetak, tabel rekap per siswa (kolom: No, Nama, NIS, Total Hadir, Izin, Sakit, Alfa, Persentase Kehadiran), dan footer dengan kolom tanda tangan kepala sekolah.
Excel (XLSX): menggunakan library Maatwebsite/Laravel-Excel. Format tabel dengan freeze header, warna baris selang-seling untuk keterbacaan, dan baris total di bagian bawah.

Filter Wajib Sebelum Generate:
Periode (wajib dipilih)
Kelas (wajib dipilih)
Rentang tanggal (opsional, default: seluruh periode)

---

## 5. User Flow
User Flow Admin – SIMAS Al-Amiin
1. Login
Admin membuka sistem
Input email & password
Jika valid → masuk ke Dashboard
Jika tidak → tampil error

2. Setup Awal Sistem 
a. Kelola Periode
Masuk menu Periode
Tambah periode (contoh: 2025/2026 - Ganjil)
Set sebagai periode aktif
b. Kelola Data Master
Input data kelas
Input data guru
Input data siswa (dan relasi ke kelas)
c. Kelola Pengguna
Buat akun:
guru
kepala sekolah
Atur role/hak akses

3. Pengaturan Jam Pelajaran (Session)
Masuk menu Session
Tambah:
nama sesi (Jam 1, Jam 2, dst)
jam mulai
jam selesai

4. Pengaturan Jadwal Pelajaran
Masuk menu Jadwal
Tentukan:
kelas
guru
mata pelajaran
hari
session (jam pelajaran)
Simpan jadwal

5. Monitoring Absensi
Masuk menu Absensi
Lihat data kehadiran siswa
Gunakan filter:
tanggal
kelas
periode

6. Koreksi / Edit Data Absensi
Pilih data absensi
Edit jika ada kesalahan input
Simpan perubahan

7. Melihat Dashboard
Lihat ringkasan:
jumlah hadir
izin / sakit / alfa
Monitoring kedisiplinan siswa

8. Generate Laporan
Masuk menu Laporan
Pilih:
periode
kelas
Generate laporan
Export / cetak

9. Ganti Periode (Jika Semester Berakhir)
Nonaktifkan periode lama
Aktifkan periode baru

10. Logout
Klik logout
Keluar dari sistem

User Flow Guru – SIMAS Al-Amiin
1. Login
Guru membuka sistem
Input email & password
Jika berhasil → masuk ke Dashboard Guru

2. Melihat Dashboard
Melihat ringkasan:
jadwal hari ini
jumlah kelas yang diajar
Sistem menampilkan jadwal sesuai hari & periode aktif

3. Melihat Jadwal Mengajar Hari Ini
Masuk ke menu Jadwal Saya
Sistem menampilkan:
kelas
jam pelajaran (session)
waktu (jam mulai – selesai)

4. Memilih Kelas untuk Absensi
Guru memilih kelas sesuai jadwal saat itu
Sistem akan:
mengecek waktu sekarang
mencocokkan dengan session
Jika sesuai:
   tombol Input Absensi aktif
Jika tidak sesuai:
   tidak bisa absen / muncul peringatan

5. Input Absensi Siswa (Manual)
Sistem menampilkan daftar siswa dalam kelas
Guru mengisi status:
Hadir
Izin
Sakit
Alfa
Klik Simpan Absensi

6. Validasi Sistem
Sistem menyimpan data dengan:
tanggal
kelas
guru
session
periode

Mencegah:
absensi ganda
input di luar jam

7. Melihat / Mengedit Absensi
Guru bisa:
melihat absensi yang sudah diinput
mengedit jika masih dalam batas waktu

8. Melihat Riwayat Absensi
Guru dapat melihat:
absensi per kelas
absensi per tanggal
Menggunakan filter:
periode
kelas

9. Logout
Guru keluar dari sistem

User Flow Kepala Sekolah – SIMAS Al-Amiin
1. Login
Kepala sekolah membuka sistem
Input email & password
Jika berhasil → masuk ke Dashboard

2. Melihat Dashboard Monitoring
Sistem menampilkan ringkasan:
jumlah kehadiran siswa
jumlah izin, sakit, alfa
persentase kehadiran

Data berdasarkan periode aktif

3. Melihat Statistik Kehadiran
Kepala sekolah dapat melihat:
grafik kehadiran siswa
perbandingan hadir vs tidak hadir
tingkat kedisiplinan

4. Melihat Data Absensi Detail
Masuk ke menu Absensi
Melihat data secara detail:
per kelas
per tanggal
per siswa
Bisa menggunakan filter:
periode
kelas
tanggal

5. Monitoring Per Kelas / Guru
Melihat:
kehadiran siswa per kelas
aktivitas absensi oleh guru

6. Melihat Laporan Absensi
Masuk ke menu Laporan
Pilih:
periode
kelas
Sistem menampilkan:
rekap kehadiran siswa
total hadir, izin, sakit, alfa

7. Export / Cetak Laporan
Kepala sekolah dapat:
mencetak laporan
menyimpan sebagai file (PDF/Excel)

8. Evaluasi & Pengambilan Keputusan
Berdasarkan data, kepala sekolah dapat:
mengevaluasi kedisiplinan siswa
memantau kinerja guru dalam absensi
mengambil kebijakan sekolah

9. Logout
Keluar dari sistem

---

## 6. Database Schema

1. users
-------------------------------
id (bigint, PK)
name (string)
email (string, unique)
password (string)
role (enum: admin, guru, kepala_sekolah)
created_at (timestamp)
updated_at (timestamp)

2. kelas
-------------------------------
id (bigint, PK)
nama_kelas (string)
created_at (timestamp)
updated_at (timestamp)

3. guru
-------------------------------
id (bigint, PK)
user_id (FK -> users.id)
nama (string)
nip (string, nullable)
is_active (boolean, default: true)   // false jika guru tidak aktif/resign
deleted_at (timestamp, nullable)     // soft delete
created_at (timestamp)
updated_at (timestamp)

4. siswa
-------------------------------
id (bigint, PK)
nama (string)
nis (string, unique)
kelas_id (FK -> kelas.id)
is_active (boolean, default: true)   // false jika siswa keluar/pindah/lulus
deleted_at (timestamp, nullable)     // soft delete
created_at (timestamp)
updated_at (timestamp)

5. periode
-------------------------------
id (bigint, PK)
tahun_ajaran (string)   // contoh: 2025/2026
semester (enum: ganjil, genap)
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)

6. sessions (jam pelajaran)
-------------------------------
id (bigint, PK)
nama_sesi (string)      
jam_mulai (time)
jam_selesai (time)
created_at (timestamp)
updated_at (timestamp)

7. jadwal
-------------------------------
id (bigint, PK)
kelas_id (FK -> kelas.id)
guru_id (FK -> guru.id)
mata_pelajaran (string)     // nama mata pelajaran yang diajarkan
hari (enum: senin, selasa, rabu, kamis, jumat, sabtu)
session_id (FK -> sessions.id)
periode_id (FK -> periode.id)
created_at (timestamp)
updated_at (timestamp)

8. absensi
-------------------------------
id (bigint, PK)
tanggal (date)
kelas_id (FK -> kelas.id)
guru_id (FK -> guru.id)
session_id (FK -> sessions.id)
periode_id (FK -> periode.id)
created_at (timestamp)
updated_at (timestamp)

9. absensi_detail
-------------------------------
id (bigint, PK)
absensi_id (FK -> absensi.id)
siswa_id (FK -> siswa.id)
status (enum: hadir, izin, sakit, alfa)
created_at (timestamp)
updated_at (timestamp)

RELATIONSHIP SUMMARY
-------------------------------
users -> guru (1:1)
kelas -> siswa (1:N)
kelas -> jadwal (1:N)
guru -> jadwal (1:N)
sessions -> jadwal (1:N)
periode -> jadwal (1:N)

jadwal -> absensi (indirect via guru + kelas + session)
absensi -> absensi_detail (1:N)
siswa -> absensi_detail (1:N)
siswa -> siswa_kelas_history (1:N)

10. siswa_kelas_history
-------------------------------
id (bigint, PK)
siswa_id (FK -> siswa.id)
kelas_id (FK -> kelas.id)
periode_id (FK -> periode.id)
tanggal_masuk (date)
tanggal_keluar (date, nullable)  // null jika masih aktif di kelas ini
keterangan (string, nullable)    // contoh: "pindah kelas", "naik kelas"
created_at (timestamp)

Catatan: Ketika siswa pindah kelas, kelas_id di tabel siswa diupdate ke kelas baru,
dan riwayat kelas lama disimpan di tabel ini. Data absensi historis tetap mengacu ke
absensi_detail yang tidak berubah, sehingga integritas data tetap terjaga.

11. audit_log
-------------------------------
id (bigint, PK)
user_id (FK -> users.id)        // siapa yang melakukan perubahan
action (enum: create, update, delete)
model (string)                  // nama tabel yang diubah (contoh: absensi_detail)
model_id (bigint)               // id record yang diubah
old_value (json, nullable)      // nilai sebelum diubah
new_value (json, nullable)      // nilai setelah diubah
ip_address (string, nullable)
created_at (timestamp)

Catatan: Audit log dicatat otomatis setiap kali ada perubahan data absensi (terutama
oleh admin). Digunakan untuk keperluan akuntabilitas dan penelusuran data.


IMPORTANT NOTES
-------------------------------
1. Hanya 1 periode boleh aktif (is_active = true)
2. Validasi absensi:
   - tidak boleh double (unique: tanggal + kelas + session + periode)
   - hanya sesuai jadwal guru yang bersangkutan
3. absensi = header (per kelas + session + tanggal)
   absensi_detail = isi per siswa
4. Gunakan index untuk:
   - siswa.nis
   - users.email
   - absensi.tanggal
   - absensi.kelas_id
   - absensi.periode_id
5. Role cukup pakai ENUM (tidak perlu spatie)
6. Gunakan SoftDeletes (deleted_at) pada tabel siswa dan guru
7. Tabel siswa_kelas_history diisi otomatis saat admin memindahkan siswa ke kelas baru
8. Audit log dicatat otomatis setiap ada perubahan data absensi oleh admin
9. Jadwal harus menyertakan mata_pelajaran agar absensi bisa diidentifikasi per mapel

-------------------------------

## 7. Authentication & Authorization

**Authentication:**
- Laravel Breeze
- Fitur lupa password menggunakan mekanisme email reset link bawaan Laravel Breeze
- Reset password: pengguna klik "Lupa Password" → input email → sistem kirim link reset ke email → pengguna klik link → input password baru
- Link reset password expired setelah 60 menit
- Admin dapat mereset password pengguna secara manual melalui panel manajemen user

**Authorization:**
- Role-Based Access Control (RBAC)
- Middleware
- Policy / Gate

**Roles:**
1. Admin
Deskripsi:
Pengelola utama sistem yang memiliki akses penuh terhadap seluruh fitur.

Hak Akses Admin:
Login ke sistem
Kelola periode (aktif/nonaktif)
Kelola data master:
siswa
guru
kelas
Kelola pengguna & role
Kelola session (jam pelajaran)
Kelola jadwal pelajaran
Melihat semua data absensi
Edit / koreksi absensi
Akses dashboard
Generate & export laporan

2. Guru
Deskripsi:
Pengguna utama yang bertugas menginput absensi siswa sesuai jadwal.
Hak Akses Guru:
Login ke sistem
Melihat dashboard pribadi
Melihat jadwal mengajar
Input absensi siswa (manual)
Mengedit absensi (dalam batas waktu)
Melihat riwayat absensi kelasnya
Filter absensi berdasarkan periode

Tidak Bisa:
Mengelola data siswa/guru/kelas
Mengatur jadwal & session
Mengelola user
Melihat data semua kelas (hanya kelas sendiri)

3. Kepala Sekolah
Deskripsi:
Pengguna yang berfokus pada monitoring dan evaluasi.
Hak Akses Kepala Sekolah:
Login ke sistem
Melihat dashboard monitoring
Melihat statistik kehadiran
Melihat semua data absensi
Filter data (kelas, tanggal, periode)
Melihat laporan absensi
Export / cetak laporan

Tidak Bisa:
Input absensi
Edit absensi
Mengelola data master
Mengatur jadwal / session
Mengelola user

## 8. Business Rules & Validation
1. Aturan Periode
Business Rules:
Hanya boleh ada 1 periode aktif dalam satu waktu
Semua data absensi harus terkait dengan periode aktif
Validation:
Saat set periode aktif → otomatis nonaktifkan periode lain
Tidak boleh input absensi jika tidak ada periode aktif

2. Aturan User & Role
Business Rules:
Setiap user wajib memiliki 1 role (admin, guru, kepala sekolah)
Guru hanya bisa mengakses data sesuai kelas/jadwalnya
Validation:
Role harus valid (enum)
Email harus unik
Password minimal (misalnya 8 karakter)

3. Aturan Data Siswa
Business Rules:
Setiap siswa harus terdaftar dalam 1 kelas aktif
Siswa memiliki identitas unik (NIS)
Siswa tidak boleh dihapus permanen dari sistem — hanya dinonaktifkan (is_active = false) agar data absensi historis tetap valid
Jika siswa pindah kelas:
kelas_id di tabel siswa diupdate ke kelas baru
riwayat kelas lama disimpan di tabel siswa_kelas_history
data absensi lama tidak berubah (tetap mengacu ke kelas dan periode saat itu)
Jika siswa keluar / lulus:
is_active diset false
siswa tidak akan muncul di daftar absensi kelas manapun
Validation:
nis → unik
nama & nis tidak boleh kosong
is_active default: true

4. Aturan Session (Jam Pelajaran)
Business Rules:
Jam pelajaran tidak boleh tumpang tindih satu sama lain
Setiap session memiliki waktu mulai & selesai yang valid
Validation:
jam_mulai < jam_selesai (validasi di backend sebelum simpan)
Saat tambah/edit session, sistem mengecek apakah range waktu baru overlap dengan session yang sudah ada:
Overlap terjadi jika: jam_mulai_baru < jam_selesai_existing AND jam_selesai_baru > jam_mulai_existing
Pengecekan dilakukan di layer aplikasi (Laravel) sebelum insert/update
Tidak menggunakan DB constraint untuk overlap karena MySQL tidak mendukung CHECK constraint berbasis baris lain; validasi dilakukan di backend
Jika overlap terdeteksi → tampilkan pesan error spesifik dengan menyebut session yang konflik

5. Aturan Jadwal
Business Rules:
Jadwal harus terikat ke:
kelas
guru
hari
session
periode
Tidak boleh ada jadwal ganda:
kelas yang sama di waktu yang sama
guru yang sama di waktu yang sama

6. Aturan Input Absensi
Business Rules:
Absensi hanya bisa dilakukan:
oleh guru yang sesuai jadwal
pada hari & jam (session) yang sesuai
Satu kelas hanya boleh 1 absensi per session per hari
Validation:
Cek waktu sekarang harus dalam range session
Cek jadwal guru sesuai

7. Aturan Absensi Detail
Business Rules:
Setiap siswa hanya memiliki 1 status absensi per session
Status hanya boleh:
hadir
izin
sakit
alfa

8. Aturan Edit Absensi (Revisi)
Business Rules:
Guru hanya dapat mengedit absensi:
pada hari yang sama saat absensi diinput
selama session (jam pelajaran) masih berlangsung, ditambah toleransi 30 menit setelah jam_selesai session
Contoh: session Jam 1 pukul 07.00–08.00, maka guru masih bisa edit hingga pukul 08.30
Setelah batas toleransi habis:
Guru tidak bisa mengedit (tombol edit dikunci/disabled)
Admin tetap bisa mengedit kapan saja tanpa batas waktu
Validation:
Sistem mengecek: NOW() <= jam_selesai_session + 30 menit
Jika tidak memenuhi syarat → tampilkan pesan "Waktu edit telah berakhir, hubungi admin"
Setiap perubahan oleh admin tercatat di audit log

9. Aturan Laporan
Business Rules:
Laporan harus berdasarkan:
periode
kelas
Data harus akurat dari absensi_detail

Validation:
Tidak boleh generate laporan jika data kosong
Filter wajib valid

10. Aturan Keamanan
Business Rules:
Semua akses harus melalui login
Data hanya bisa diakses sesuai role
Validation:
Middleware auth
Role checking di setiap fitur

## 9. UI Component & Design System
**Framework:** Tailwind CSS

Design System
1. Warna
Primary: Hijau (#16A34A)
Secondary: Hijau muda (#22C55E)
Background: Putih (#FFFFFF)
Surface: Abu muda (#F9FAFB)
Text: Hitam/abu (#111827, #6B7280)
Status:
Hadir: Hijau
Izin: Kuning
Sakit: Biru
Alfa: Merah

2. Typography
Font: Inter / Poppins
Heading: tegas (bold/semibold)
Body: 14px (mudah dibaca)

3. Style
Clean & minimal
Rounded (8–12px)
Shadow ringan
Fokus pada keterbacaan data

UI Components Utama
1. Button
Primary (hijau) → aksi utama
Secondary (outline) → aksi tambahan
2. Input & Form
Input text
Select (status absensi)
Validasi sederhana
3. Table
Menampilkan data siswa & absensi
Support filter & search
4. Card
Untuk dashboard & ringkasan data
5. Badge
Menampilkan status:
Hadir, Izin, Sakit, Alfa
6. Navbar & Sidebar
Sidebar: navigasi utama
Navbar: info user & logout
7. Modal
Konfirmasi (hapus, edit, dll)
8. toast notifikation global menggunakan event

Prinsip UI/UX
Sederhana & mudah digunakan guru
Fokus pada kecepatan input absensi
Default “Hadir” untuk semua siswa
Aksi penting menggunakan warna hijau

## 10. Security
- Autentikasi & otorisasi berbasis role
- Proteksi CSRF, XSS, dan SQL Injection
- Validasi input di backend
- Pembatasan akses sesuai user
- Validasi business logic (jadwal & session)
- Penggunaan database constraint untuk integritas data
- Audit log untuk setiap perubahan data absensi oleh admin (mencatat: user, waktu, data sebelum & sesudah)
- Soft delete pada data siswa dan guru agar data historis tidak hilang
- Link reset password expired dalam 60 menit dan hanya bisa digunakan sekali

## 11. Performance & Scalability

**Target Performa (SLA):**
- Halaman dashboard & daftar data: load ≤ 2 detik pada koneksi normal
- Proses simpan absensi: response ≤ 1 detik
- Generate laporan PDF/Excel: selesai ≤ 5 detik untuk data hingga 500 siswa
- Sistem harus mampu menangani minimal 50 pengguna concurrent tanpa degradasi signifikan

**Teknik Implementasi:**
- Gunakan lazy loading halaman (Inertia.js)
- Hindari render ulang berlebihan di Vue component
- Gunakan component reusable
- Gunakan debounce (300ms) untuk input search/filter
- Gunakan pagination (default 25 baris per halaman) untuk semua tabel data
- Gunakan Eager Loading (with()) di Eloquent untuk menghindari N+1 query
- Tambahkan database index pada kolom yang sering diquery: siswa.nis, users.email, absensi.tanggal, absensi.kelas_id, absensi.periode_id
---