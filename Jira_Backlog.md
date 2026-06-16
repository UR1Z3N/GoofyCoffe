# 🎯 Jira Backlog: GoofyCafe POS

Saya telah merevisi seluruh *backlog* ini. Sekarang, **kesepuluh User Story di bawah ini 100% menyesuaikan dengan fitur yang benar-benar ada di *source code*** proyek Anda (termasuk fitur panduan interaktif dengan `driver.js`, halaman FAQ, dan *routing* navigasi `index.php`). Tidak ada fitur fiktif (seperti cetak struk atau CRUD admin) yang dimasukkan.

---

## ⚙️ EPIC 0: Project Setup & Architecture
**Tujuan Epic**: Pekerjaan teknis fondasi awal untuk menyiapkan *environment* pengembangan proyek.

### 🛠️ Task 0.1: Database Setup & Configuration
| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 Highest |
| **Tipe Pekerjaan** | *Technical Debt* |

*Daftar pekerjaan teknis (Sub-tasks):*
- [ ] Mengeksekusi file `database.sql` ke dalam MySQL/MariaDB.
- [ ] Membuat file koneksi PDO di `config/database.php`.
- [ ] Mengeksekusi `reset_db.php` untuk memuat data *dummy* kategori dan menu awal.

---

## 📂 EPIC 1: Antarmuka Utama & Navigasi Dasar
**Tujuan Epic**: Membangun kerangka navigasi utama web (*layouting*) dan pusat bantuan informasi.

### 🏷️ Story 1.1: Kerangka Layout Responsif & Header Navigasi
> **Sebagai** Pengguna, **saya ingin** melihat tata letak antarmuka yang bersih dengan menu navigasi statis di bagian atas **agar** saya bisa berpindah halaman dengan mudah dari menu mana pun.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 5 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Navigasi Antar Halaman**
* **Given**: Pengguna membuka alamat situs GoofyCafe.
* **When**: Pengguna mengklik tombol "Kasir", "Riwayat", atau "Laporan" pada *Header*.
* **Then**: Parameter URL berubah menjadi `?page=...` dan konten di area utama web langsung berubah menyesuaikan pilihan tersebut.

#### 🛠️ Task Utama: Implementasi Layout Index
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Memuat TailwindCSS dan FontAwesome via CDN di `<head>`.
- [ ] Membuat *Header* statis warna hijau yang memuat Logo Brand dan Logo Teks.
- [ ] Menulis logika `switch-case` di file utama `index.php` untuk *routing* halaman berdasar parameter `$_GET['page']`.

---

### 🏷️ Story 1.2: Halaman FAQ & Bantuan Informasi
> **Sebagai** Pengguna Baru, **saya ingin** dapat mengakses halaman FAQ **agar** saya bisa membaca panduan tekstual terkait cara menggunakan sistem POS ini.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🟢 Low |
| **Tingkat Kesulitan** | 🟢 Mudah |
| **Story Points** | 2 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Akses Halaman FAQ**
* **When**: Pengguna mengklik tombol "FAQ" pada Header (logo tanda seru/info).
* **Then**: Sistem menampilkan halaman statis berisi teks panduan cara penggunaan (*views/faq/index.php*).

#### 🛠️ Task Utama: Pembuatan Konten FAQ
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Mendaftarkan *routing* `faq` pada array `allowed_pages` di `index.php`.
- [ ] Mendesain dan memasukkan salinan teks dari `Manual_Book.md` ke dalam halaman `views/faq/index.php`.

---

## 📂 EPIC 2: Transaksi Kasir (POS Core)
**Tujuan Epic**: Memfasilitasi seluruh proses utama kasir mulai dari pemilihan menu hingga pembayaran.

### 🏷️ Story 2.1: Menampilkan & Memfilter Katalog Menu
> **Sebagai** Kasir, **saya ingin** melihat daftar seluruh menu dan memfilternya berdasarkan kategori **agar** saya bisa dengan cepat menemukan pesanan pelanggan.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 5 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Tampilan Default & Filter Halaman POS**
* **Given**: Halaman POS termuat dengan sempurna.
* **When**: Kasir mengklik tombol filter kategori tertentu (misal: Minuman).
* **Then**: Sistem memfilter dan merender ulang grid katalog hanya untuk produk di kategori tersebut.

#### 🛠️ Task Utama: Menyiapkan Modul Katalog Menu
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Mendesain komponen navigasi UI kategori (*Pills*) di `views/pos/index.php`.
- [ ] Memanggil class `Menu.php` untuk me-render daftar menu ke dalam HTML Card (lengkap dengan logika *fallback* CSS gambar via `menu-image-container`).

---

### 🏷️ Story 2.2: Manajemen Keranjang Pesanan
> **Sebagai** Kasir, **saya ingin** menambah atau menghapus item dan menulis catatan di keranjang pesanan **agar** permintaan khusus (seperti "es dipisah") dapat tercatat.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 5 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Tambah, Kurangi, dan Catatan Item**
* **When**: Kasir mengklik menu yang sama, mengatur kuantitas, atau mengisi form input "Catatan" (Notes).
* **Then**: *Total Bayar* terakumulasi otomatis dan item tidak duplikat baris (kecuali qty-nya bertambah). Jika qty 0, baris pesanan dihapus.

#### 🛠️ Task Utama: Membangun Fungsionalitas Keranjang
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Membuat file komponen `includes/order_list.php` untuk menampilkan daftar belanjaan.
- [ ] Menulis logika array di dalam variabel `$_SESSION['cart']` untuk menyimpan sesi produk, qty, dan text-input *notes*.

---

### 🏷️ Story 2.3: Proses Checkout Pembayaran
> **Sebagai** Kasir, **saya ingin** memproses pesanan dengan pilihan pembayaran Tunai atau Digital **agar** transaksi selesai dan tersimpan ke dalam sistem.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🔴 Sulit |
| **Story Points** | 8 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Validasi dan Penyimpanan Pesanan**
* **When**: Kasir menekan tombol "Proses", memilih metode Tunai/Digital, lalu mensubmit formulir konfirmasi.
* **Then**: Tabel `orders` dan `order_items` terisi di database, kemudian session keranjang dikosongkan.

#### 🛠️ Task Utama: Integrasi Checkout & Database
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Membuat file target `order_proses.php` untuk menangkap POST konfirmasi pembayaran.
- [ ] Menggunakan logika *DB Transaction* untuk melakukan `INSERT` nomor order unik (*UNIQID/DateTime*).

---

### 🏷️ Story 2.4: Panduan Interaktif Pengguna Baru (Tour Guide)
> **Sebagai** Kasir Baru, **saya ingin** ada fitur panduan interaktif langsung di atas halaman POS **agar** saya cepat memahami fungsi tombol-tombol yang ada di kasir.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🟡 Medium |
| **Tingkat Kesulitan** | 🟢 Mudah |
| **Story Points** | 3 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Menjalankan Panduan Interaktif**
* **When**: Kasir menekan tombol "Panduan" bergambar ikon tanya di Header.
* **Then**: Muncul pop-up *overlay/tour* (menggunakan *driver.js*) yang menyorot satu-persatu bagian halaman (seperti menyorot keranjang belanja atau tombol checkout) dengan teks penjelasan.

#### 🛠️ Task Utama: Pemasangan Library Driver.js
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Mengintegrasikan CSS dan JS dari library `driver.js` ke dalam `index.php`.
- [ ] Membuat fungsi javascript `startTour()` yang mengkonfigurasi urutan elemen target sorotan *tour* di halaman `pos`.

---

## 📂 EPIC 3: Riwayat & Pelacakan Transaksi
**Tujuan Epic**: Modul untuk melacak pesanan di masa lalu.

### 🏷️ Story 3.1: Tabel Daftar Riwayat Pesanan
> **Sebagai** Manajer Kafe, **saya ingin** melihat daftar seluruh transaksi yang sudah selesai disajikan dalam bentuk tabel **agar** saya mudah memantaunya.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟢 Mudah |
| **Story Points** | 3 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Tampilan Tabel Riwayat**
* **Given**: Manajer membuka halaman Riwayat.
* **Then**: Muncul tabel dengan kolom No Order, Waktu, Metode Pembayaran (warna *Badge* hijau/biru), dan Total.

#### 🛠️ Task Utama: Pembuatan Modul Tabel Riwayat
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Menulis query `$orderModel->getOrdersHistory()` untuk memanggil daftar dari database.
- [ ] Me-render iterasi `foreach` data tersebut ke dalam file `views/history/index.php`.

---

### 🏷️ Story 3.2: Filter Riwayat Rentang Waktu
> **Sebagai** Manajer Kafe, **saya ingin** memfilter tabel riwayat berdasarkan "Dari Tanggal" dan "Sampai Tanggal" **agar** pencarian laporan spesifik lebih cepat.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🟡 Medium |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 3 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Aksi Memfilter Waktu**
* **When**: Manajer mengisi input `start_date` dan `end_date`, lalu menekan Filter.
* **Then**: Query tabel diperbarui hanya mengambil data transaksi yang berada dalam limit waktu spesifik tersebut.

#### 🛠️ Task Utama: Integrasi Filter Tanggal
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Membuat desain HTML Form GET dengan input kalender `type="date"`.
- [ ] Menangkap parameter `start_date` dan `end_date` untuk disisipkan ke klausul `WHERE` query `getOrdersHistory`.

---

### 🏷️ Story 3.3: Pop-up Modal Rincian Item Transaksi
> **Sebagai** Manajer Kafe, **saya ingin** melihat secara detail menu apa saja (termasuk catatannya) pada sebuah transaksi tertentu **agar** memudahkan pengecekan nota ulang.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 5 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Memuat Detail Menggunakan AJAX**
* **Given**: Tombol "Lihat Detail" diklik.
* **Then**: Muncul Modal *Pop-up* berlayar gelap (Background hitam transparan).
* **And**: Terdapat animasi *loading* sejenak, sebelum daftar item `order_items` muncul di dalam Modal tersebut.

#### 🛠️ Task Utama: Implementasi Pop-up Detail
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Mendesain kerangka `div id="detail-modal"` (tersembunyi) di `views/history/index.php`.
- [ ] Menyiapkan file endpoint PHP (`controllers/get_order_details.php`) yang mereturn tipe data JSON.
- [ ] Menulis fungsi *Javascript Fetch* `viewDetails()` yang menembakkan request GET dan menggambar ulang HTML respon JSON ke dalam modal.

---

## 📂 EPIC 4: Dashboard Laporan Penjualan
**Tujuan Epic**: Visualisasi ringkasan kinerja penjualan.

### 🏷️ Story 4.1: Dashboard Ringkasan Metrik Harian
> **Sebagai** Pemilik Kafe, **saya ingin** melihat kartu metrik (Total Pendapatan, Transaksi, Tunai, Digital) **agar** saya dapat menilai performa bisnis.

| Atribut | Keterangan |
| :--- | :--- |
| **Prioritas** | 🔴 High |
| **Tingkat Kesulitan** | 🟡 Sedang |
| **Story Points** | 5 |

#### ✅ Acceptance Criteria (Kriteria Penerimaan)
**Skenario 1: Kalkulasi Akurat Berdasarkan Database**
* **Given**: Pemilik membuka halaman laporan.
* **Then**: Angka di Kartu Total Pendapatan harus secara akurat mewakili penjumlahan (`SUM`) pesanan hari tersebut, diikuti dengan informasi pembayaran tunai dan digital.

#### 🛠️ Task Utama: Pembangunan Dashboard Laporan
*Daftar Sub-tasks untuk dikerjakan Developer:*
- [ ] Menyusun file view `views/report/index.php`.
- [ ] Menjalankan query agregasi pendapatan harian dan mendistribusikannya ke desain HTML 4 Kotak *Summary Cards*.
