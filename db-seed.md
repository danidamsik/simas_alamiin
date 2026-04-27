> Digunakan untuk keperluan development, testing, dan demo sistem.
> Semua data di bawah ini harus tersedia setelah menjalankan `php artisan db:seed`.

Periode
| id | tahun_ajaran | semester | tanggal_mulai | tanggal_selesai | is_active |
|----|---------------|----------|----------------|-----------------|-----------|
| 1  | 2024/2025     | genap    | 2025-01-01     | 2025-06-30      | false     |
| 2  | 2025/2026     | ganjil   | 2025-07-01     | 2025-12-31      | **true**  |

> Hanya periode id=2 yang aktif. Semua data absensi dummy mengacu ke periode ini.

Kelas
| id | nama_kelas |
|----|------------|
| 1  | X IPA 1    |
| 2  | X IPS 1    |
| 3  | XI IPA 1   |
| 4  | XI IPS 1   |

Users & Roles
| id | name            | email                       | password    | role           |
|----|-----------------|-----------------------------|-------------|----------------|
| 1  | Administrator   | admin@alamiin.sch.id        | admin12345  | admin          |
| 2  | Budi Santoso    | budi.santoso@alamiin.sch.id | guru12345   | guru           |
| 3  | Siti Rahayu     | siti.rahayu@alamiin.sch.id  | guru12345   | guru           |
| 4  | Ahmad Fauzi     | ahmad.fauzi@alamiin.sch.id  | guru12345   | guru           |
| 5  | Dewi Lestari    | dewi.lestari@alamiin.sch.id | guru12345   | guru           |
| 6  | Dr. H. Suparman | kepsek@alamiin.sch.id       | kepsek12345 | kepala_sekolah |

Guru
| id | user_id | nama         | nip                | is_active |
|----|---------|--------------|--------------------|-----------|
| 1  | 2       | Budi Santoso | 198501012010011001 | true      |
| 2  | 3       | Siti Rahayu  | 198703152012012002 | true      |
| 3  | 4       | Ahmad Fauzi  | 199002202014011003 | true      |
| 4  | 5       | Dewi Lestari | 199105102015012004 | true      |

Siswa
Kelas X IPA 1 — kelas_id = 1 (18 Siswa)
| id | nama               | nis        | kelas_id |
|----|--------------------|------------|----------|
| 1  | Andi Pratama       | 2526001001 | 1        |
| 2  | Bella Safitri      | 2526001002 | 1        |
| 3  | Cahya Nugroho      | 2526001003 | 1        |
| 4  | Dina Marlina       | 2526001004 | 1        |
| 5  | Eko Setiawan       | 2526001005 | 1        |
| 6  | Farah Aulia        | 2526001006 | 1        |
| 7  | Gilang Ramadan     | 2526001007 | 1        |
| 8  | Hani Permatasari   | 2526001008 | 1        |
| 9  | Ilham Maulana      | 2526001009 | 1        |
| 10 | Jasmine Putri      | 2526001010 | 1        |
| 11 | Kevin Adriansyah   | 2526001011 | 1        |
| 12 | Laila Nurfadilah   | 2526001012 | 1        |
| 13 | Muhammad Rizky     | 2526001013 | 1        |
| 14 | Nadia Khairunnisa  | 2526001014 | 1        |
| 15 | Oscar Firmansyah   | 2526001015 | 1        |
| 16 | Putri Ramadhani    | 2526001016 | 1        |
| 17 | Rafi Akbar         | 2526001017 | 1        |
| 18 | Salsabila Zahra    | 2526001018 | 1        |

Kelas X IPS 1 — kelas_id = 2 (18 Siswa)
| id | nama               | nis        | kelas_id |
|----|--------------------|------------|----------|
| 19 | Taufik Hidayat     | 2526002001 | 2        |
| 20 | Ulfa Nuraini       | 2526002002 | 2        |
| 21 | Vino Saputra       | 2526002003 | 2        |
| 22 | Winda Agustina     | 2526002004 | 2        |
| 23 | Xander Pratomo     | 2526002005 | 2        |
| 24 | Yani Kusumawati    | 2526002006 | 2        |
| 25 | Zaki Maulana       | 2526002007 | 2        |
| 26 | Alya Rahmawati     | 2526002008 | 2        |
| 27 | Bram Septian       | 2526002009 | 2        |
| 28 | Citra Dewi         | 2526002010 | 2        |
| 29 | Dafa Ardiansyah    | 2526002011 | 2        |
| 30 | Elsa Fitriani      | 2526002012 | 2        |
| 31 | Farhan Nugraha     | 2526002013 | 2        |
| 32 | Gita Puspita       | 2526002014 | 2        |
| 33 | Hendra Kurniawan   | 2526002015 | 2        |
| 34 | Indah Permata      | 2526002016 | 2        |
| 35 | Jaka Santosa       | 2526002017 | 2        |
| 36 | Kiki Amalia        | 2526002018 | 2        |

Kelas XI IPA 1 — kelas_id = 3 (17 Siswa)
| id | nama               | nis        | kelas_id |
|----|--------------------|------------|----------|
| 37 | Luki Prasetyo      | 2425003001 | 3        |
| 38 | Maya Anggraini     | 2425003002 | 3        |
| 39 | Nando Satria       | 2425003003 | 3        |
| 40 | Olivia Sari        | 2425003004 | 3        |
| 41 | Panji Wicaksono    | 2425003005 | 3        |
| 42 | Qori Fadillah      | 2425003006 | 3        |
| 43 | Rangga Pratama     | 2425003007 | 3        |
| 44 | Silvana Mardiana   | 2425003008 | 3        |
| 45 | Tri Wahyudi        | 2425003009 | 3        |
| 46 | Uswatun Hasanah    | 2425003010 | 3        |
| 47 | Wahyu Setiadi      | 2425003011 | 3        |
| 48 | Yessica Amalia     | 2425003012 | 3        |
| 49 | Zulfikar Rahman    | 2425003013 | 3        |
| 50 | Aditya Firmansyah  | 2425003014 | 3        |
| 51 | Bunga Citra        | 2425003015 | 3        |
| 52 | Candra Wijaya      | 2425003016 | 3        |
| 53 | Dini Pratiwi       | 2425003017 | 3        |

Kelas XI IPS 1 — kelas_id = 4 (17 Siswa)
| id | nama               | nis        | kelas_id |
|----|--------------------|------------|----------|
| 54 | Ervan Maulana      | 2425004001 | 4        |
| 55 | Fitria Hasanah     | 2425004002 | 4        |
| 56 | Galih Supriyanto   | 2425004003 | 4        |
| 57 | Hilda Febriani     | 2425004004 | 4        |
| 58 | Ivan Kurniawan     | 2425004005 | 4        |
| 59 | Junita Sari        | 2425004006 | 4        |
| 60 | Khairul Anwar      | 2425004007 | 4        |
| 61 | Linda Apriani      | 2425004008 | 4        |
| 62 | Mirza Fathullah    | 2425004009 | 4        |
| 63 | Nurhaliza          | 2425004010 | 4        |
| 64 | Okta Ridwan        | 2425004011 | 4        |
| 65 | Pita Lestari       | 2425004012 | 4        |
| 66 | Qodri Maulana      | 2425004013 | 4        |
| 67 | Rahmi Oktaviani    | 2425004014 | 4        |
| 68 | Sandi Permana      | 2425004015 | 4        |
| 69 | Tari Setyowati     | 2425004016 | 4        |
| 70 | Ujang Sopian       | 2425004017 | 4        |

> **Total: 70 siswa aktif** (18 + 18 + 17 + 17)
> Format NIS: 2 digit tahun masuk + 2 digit kode kelas + 3 digit nomor urut

12.6 Sessions (Jam Pelajaran)
| id | nama_sesi | jam_mulai | jam_selesai |
|----|-----------|-----------|-------------|
| 1  | Jam 1     | 07:00     | 07:45       |
| 2  | Jam 2     | 07:45     | 08:30       |
| 3  | Jam 3     | 08:30     | 09:15       |
| 4  | Jam 4     | 09:30     | 10:15       |
| 5  | Jam 5     | 10:15     | 11:00       |
| 6  | Jam 6     | 11:00     | 11:45       |
| 7  | Jam 7     | 12:30     | 13:15       |
| 8  | Jam 8     | 13:15     | 14:00       |

> Istirahat 1: 09:15-09:30 | Istirahat 2: 11:45-12:30 (tidak dibuat sebagai session)

12.7 Jadwal Pelajaran (periode_id = 2)
Senin
| id | kelas_id | guru_id | mata_pelajaran   | hari  | session_id |
|----|----------|---------|------------------|-------|------------|
| 1  | 1        | 1       | Matematika       | senin | 1          |
| 2  | 1        | 2       | Bahasa Indonesia | senin | 3          |
| 3  | 2        | 4       | Ekonomi          | senin | 1          |
| 4  | 2        | 3       | Fisika           | senin | 4          |
| 5  | 3        | 1       | Matematika       | senin | 4          |
| 6  | 3        | 3       | Biologi          | senin | 6          |
| 7  | 4        | 2       | Bahasa Indonesia | senin | 2          |
| 8  | 4        | 4       | Sosiologi        | senin | 5          |

Selasa
| id | kelas_id | guru_id | mata_pelajaran   | hari   | session_id |
|----|----------|---------|------------------|--------|------------|
| 9  | 1        | 3       | Fisika           | selasa | 2          |
| 10 | 1        | 4       | Ekonomi          | selasa | 5          |
| 11 | 2        | 1       | Matematika       | selasa | 3          |
| 12 | 2        | 2       | Bahasa Indonesia | selasa | 6          |
| 13 | 3        | 2       | Bahasa Indonesia | selasa | 1          |
| 14 | 3        | 4       | Ekonomi          | selasa | 3          |
| 15 | 4        | 1       | Matematika       | selasa | 4          |
| 16 | 4        | 3       | Kimia            | selasa | 7          |

Rabu
| id | kelas_id | guru_id | mata_pelajaran   | hari | session_id |
|----|----------|---------|------------------|------|------------|
| 17 | 1        | 1       | Matematika       | rabu | 2          |
| 18 | 1        | 3       | Biologi          | rabu | 4          |
| 19 | 2        | 4       | Sosiologi        | rabu | 2          |
| 20 | 2        | 1       | Matematika       | rabu | 5          |
| 21 | 3        | 3       | Fisika           | rabu | 2          |
| 22 | 3        | 1       | Matematika       | rabu | 5          |
| 23 | 4        | 2       | Bahasa Indonesia | rabu | 3          |
| 24 | 4        | 4       | Ekonomi          | rabu | 6          |

Kamis
| id | kelas_id | guru_id | mata_pelajaran   | hari  | session_id |
|----|----------|---------|------------------|-------|------------|
| 25 | 1        | 2       | Bahasa Indonesia | kamis | 1          |
| 26 | 1        | 4       | Sosiologi        | kamis | 4          |
| 27 | 2        | 3       | Kimia            | kamis | 1          |
| 28 | 2        | 2       | Bahasa Indonesia | kamis | 5          |
| 29 | 3        | 4       | Geografi         | kamis | 3          |
| 30 | 3        | 2       | Bahasa Indonesia | kamis | 6          |
| 31 | 4        | 1       | Matematika       | kamis | 2          |
| 32 | 4        | 3       | Fisika           | kamis | 5          |

#Jumat
| id | kelas_id | guru_id | mata_pelajaran   | hari  | session_id |
|----|----------|---------|------------------|-------|------------|
| 33 | 1        | 3       | Kimia            | jumat | 1          |
| 34 | 1        | 2       | Bahasa Indonesia | jumat | 3          |
| 35 | 2        | 1       | Matematika       | jumat | 1          |
| 36 | 2        | 4       | Geografi         | jumat | 3          |
| 37 | 3        | 1       | Matematika       | jumat | 2          |
| 38 | 3        | 3       | Biologi          | jumat | 4          |
| 39 | 4        | 4       | Sosiologi        | jumat | 2          |
| 40 | 4        | 2       | Bahasa Indonesia | jumat | 4          |

---

Absensi & Absensi Detail (Simulasi 1 Minggu)

> Periode simulasi: **Senin 21 Juli – Jumat 25 Juli 2025**
Header Absensi (tabel `absensi`) — Total 40 record
Setiap baris = 1 sesi mengajar yang telah diinput oleh guru.

| absensi_id | tanggal    | kelas_id | guru_id | session_id | periode_id |
|------------|------------|----------|---------|------------|------------|
| 1          | 2025-07-21 | 1        | 1       | 1          | 2          |
| 2          | 2025-07-21 | 1        | 2       | 3          | 2          |
| 3          | 2025-07-21 | 2        | 4       | 1          | 2          |
| 4          | 2025-07-21 | 2        | 3       | 4          | 2          |
| 5          | 2025-07-21 | 3        | 1       | 4          | 2          |
| 6          | 2025-07-21 | 3        | 3       | 6          | 2          |
| 7          | 2025-07-21 | 4        | 2       | 2          | 2          |
| 8          | 2025-07-21 | 4        | 4       | 5          | 2          |
| 9          | 2025-07-22 | 1        | 3       | 2          | 2          |
| 10         | 2025-07-22 | 1        | 4       | 5          | 2          |
| 11         | 2025-07-22 | 2        | 1       | 3          | 2          |
| 12         | 2025-07-22 | 2        | 2       | 6          | 2          |
| 13         | 2025-07-22 | 3        | 2       | 1          | 2          |
| 14         | 2025-07-22 | 3        | 4       | 3          | 2          |
| 15         | 2025-07-22 | 4        | 1       | 4          | 2          |
| 16         | 2025-07-22 | 4        | 3       | 7          | 2          |
| 17         | 2025-07-23 | 1        | 1       | 2          | 2          |
| 18         | 2025-07-23 | 1        | 3       | 4          | 2          |
| 19         | 2025-07-23 | 2        | 4       | 2          | 2          |
| 20         | 2025-07-23 | 2        | 1       | 5          | 2          |
| 21         | 2025-07-23 | 3        | 3       | 2          | 2          |
| 22         | 2025-07-23 | 3        | 1       | 5          | 2          |
| 23         | 2025-07-23 | 4        | 2       | 3          | 2          |
| 24         | 2025-07-23 | 4        | 4       | 6          | 2          |
| 25         | 2025-07-24 | 1        | 2       | 1          | 2          |
| 26         | 2025-07-24 | 1        | 4       | 4          | 2          |
| 27         | 2025-07-24 | 2        | 3       | 1          | 2          |
| 28         | 2025-07-24 | 2        | 2       | 5          | 2          |
| 29         | 2025-07-24 | 3        | 4       | 3          | 2          |
| 30         | 2025-07-24 | 3        | 2       | 6          | 2          |
| 31         | 2025-07-24 | 4        | 1       | 2          | 2          |
| 32         | 2025-07-24 | 4        | 3       | 5          | 2          |
| 33         | 2025-07-25 | 1        | 3       | 1          | 2          |
| 34         | 2025-07-25 | 1        | 2       | 3          | 2          |
| 35         | 2025-07-25 | 2        | 1       | 1          | 2          |
| 36         | 2025-07-25 | 2        | 4       | 3          | 2          |
| 37         | 2025-07-25 | 3        | 1       | 2          | 2          |
| 38         | 2025-07-25 | 3        | 3       | 4          | 2          |
| 39         | 2025-07-25 | 4        | 4       | 2          | 2          |
| 40         | 2025-07-25 | 4        | 2       | 4          | 2          |

---

contoh Detail Absensi — absensi_id = 1
(Senin 21 Juli, X IPA 1, Matematika, Jam 1 oleh Budi Santoso)
| absensi_detail_id | absensi_id | siswa_id | status |
|-------------------|------------|----------|--------|
| 1                 | 1          | 1        | hadir  |
| 2                 | 1          | 2        | hadir  |
| 3                 | 1          | 3        | sakit  |
| 4                 | 1          | 4        | hadir  |
| 5                 | 1          | 5        | hadir  |
| 6                 | 1          | 6        | izin   |
| 7                 | 1          | 7        | hadir  |
| 8                 | 1          | 8        | hadir  |
| 9                 | 1          | 9        | hadir  |
| 10                | 1          | 10       | hadir  |
| 11                | 1          | 11       | alfa   |
| 12                | 1          | 12       | hadir  |
| 13                | 1          | 13       | hadir  |
| 14                | 1          | 14       | hadir  |
| 15                | 1          | 15       | sakit  |
| 16                | 1          | 16       | hadir  |
| 17                | 1          | 17       | hadir  |
| 18                | 1          | 18       | hadir  |

> Sisa 39 header absensi menggunakan distribusi status acak berbobot via seeder.

Distribusi Status Absensi (untuk Seeder)
| Status | Bobot | Keterangan                     |
|--------|-------|--------------------------------|
| hadir  | 85%   | Mayoritas siswa hadir          |
| sakit  | 6%    | Beberapa siswa sakit per minggu|
| izin   | 5%    | Siswa izin keperluan tertentu  |
| alfa   | 4%    | Tanpa keterangan               |

**Urutan eksekusi wajib (dependency order):**
Periode -> Kelas -> User -> Guru -> Siswa -> Session -> Jadwal -> Absensi
