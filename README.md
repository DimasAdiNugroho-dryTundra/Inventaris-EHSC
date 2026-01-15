

# **Aplikasi Pengelolaan Barang dan Inventaris pada Kantor EHSC PT. Buana Karya Bhakti**

![Status](https://img.shields.io/badge/Status-Stable-success) ![Platform](https://img.shields.io/badge/Platform-Web-blue) ![Backend](https://img.shields.io/badge/Backend-PHP-orange) ![Database](https://img.shields.io/badge/Database-MySQL-blue)

Aplikasi **Pengelolaan Barang dan Inventaris pada Kantor EHSC PT. Buana Karya Bhakti** merupakan sistem berbasis web yang dirancang untuk mengelola data inventaris dan aset secara terpusat, terstruktur, dan terdokumentasi dengan baik. Sistem ini mencakup seluruh siklus inventaris mulai dari permintaan barang, penerimaan barang, pencatatan inventaris, hingga kontrol kondisi barang secara berkala.

Aplikasi dikembangkan untuk mendukung proses administrasi dan pengawasan inventaris agar lebih efisien, akurat, serta mudah ditelusuri.

---

## 🎯 Tujuan Pengembangan (Tugas PKL)

Pengembangan sistem ini bertujuan untuk:

* Menerapkan ilmu pengembangan perangkat lunak berbasis web ke dalam sistem nyata.
* Menggantikan pencatatan inventaris manual dengan sistem terkomputerisasi.
* Meningkatkan akurasi dan konsistensi data inventaris.
* Mempermudah proses kontrol dan pelaporan kondisi barang secara berkala.
* Menyediakan informasi inventaris yang cepat dan mudah diakses.

---

## 🚀 Fitur Utama

### 🔐 Autentikasi & Hak Akses

* Sistem login dengan **multi-role user**:

  * **Administrasi**
  * **Staff**
  * **Petugas Kontrol**
* Setiap role memiliki hak akses dan tampilan dashboard berbeda.

### 🗂️ Manajemen Data Master

* Manajemen **User**
* Manajemen **Departemen**
* Manajemen **Kategori Barang**
* Manajemen **Ruangan**

### 📦 Manajemen Inventaris

* Pencatatan **permintaan barang**
* Pencatatan **penerimaan barang**
* Pembuatan dan pengelolaan **data inventaris**
* Penempatan inventaris berdasarkan ruangan dan departemen

### 🔍 Kontrol Barang Berkala

* Kontrol kondisi barang per periode (**caturwulan**)
* Pencatatan kondisi:

  * Barang Baik
  * Barang Rusak
  * Barang Pindah
  * Barang Hilang

### 🔄 Penanganan Kondisi Khusus

* Pencatatan **kerusakan barang** (dengan dokumentasi)
* Pencatatan **perpindahan barang** antar ruangan
* Pencatatan **kehilangan barang**

### 🔲 QR Code Inventaris

* Setiap inventaris memiliki **QR Code unik**
* QR Code digunakan untuk menampilkan detail barang secara cepat

### 📊 Sistem Laporan

* Laporan Permintaan Barang
* Laporan Penerimaan Barang
* Laporan Inventaris
* Laporan Hasil Kontrol Barang
* Laporan Kerusakan, Perpindahan, dan Kehilangan Barang

---

## 🏗️ Gambaran Sistem

Alur kerja sistem:

1. User login sesuai role
2. Input permintaan dan penerimaan barang
3. Barang dicatat ke dalam inventaris
4. Petugas melakukan kontrol berkala
5. Sistem menyimpan data dan menghasilkan laporan otomatis

---

## 🗂️ Struktur Direktori

```
Inventaris-EHSC/
├── database/          # File database (.sql)
├── layouts/           # Template layout
├── lib/               # Library & helper
├── pages/             # Halaman aplikasi
├── report/            # Modul laporan
├── server/            # Logika backend
├── upload/            # Upload file & foto
├── index.php          # Entry point aplikasi
└── README.md          # Dokumentasi
```

---

## 💾 Database

Sistem menggunakan **MySQL (Relational Database)** dengan tabel utama:

* user
* departemen
* kategori
* ruangan
* permintaan_barang
* penerimaan_barang
* inventaris
* kontrol_barang
* kerusakan_barang
* perpindahan_barang
* kehilangan_barang

Relasi antar tabel dirancang untuk menjaga konsistensi dan integritas data.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi      |
| -------- | -------------- |
| Backend  | PHP            |
| Database | MySQL          |
| Frontend | HTML, CSS      |
| Server   | Apache (XAMPP) |

---

## ⚙️ Instalasi & Konfigurasi

1. **Clone repository**

   ```bash
   git clone https://github.com/DimasAdiNugroho-dryTundra/Inventaris-EHSC
   ```

2. **Pindahkan ke server lokal**

   ```
   htdocs/Inventaris-EHSC
   ```

3. **Import database**

   * Buat database baru di phpMyAdmin
   * Import file `.sql` dari folder `database`

4. **Konfigurasi koneksi database**
   Sesuaikan file konfigurasi database di folder server.

5. **Jalankan aplikasi**

   ```
   http://localhost/Inventaris-EHSC/
   ```

---

## 👤 Role Pengguna

* **Administrasi**
  Mengelola seluruh data sistem dan laporan

* **Staff**
  Input dan monitoring data inventaris

* **Petugas Kontrol**
  Melakukan kontrol kondisi barang secara berkala

---

📌 **Catatan**

Proyek **Aplikasi Pengelolaan Barang dan Inventaris pada Kantor EHSC PT. Buana Karya Bhakti** dikembangkan sebagai bagian dari tugas Praktek Kerja Lapangan (PKL) dan digunakan untuk keperluan pembelajaran serta implementasi sistem inventaris berbasis web.

Penggunaan, pengembangan, atau distribusi lebih lanjut menyesuaikan dengan kebutuhan dan kebijakan institusi terkait.

