# 📌 To-Do List App (PHP + Bootstrap 5)

## 1. Informasi Umum
- **Nama Aplikasi**: To-Do List
- **Dibuat oleh**: [Nama Peserta Ujian]
- **Skema**: Junior Web Programmer – Uji Kompetensi KKNl
- **Teknologi yang digunakan**:
  - PHP 8+
  - HTML5 + CSS3 (Bootstrap 5, Bootstrap Icons)
  - Session Storage (tanpa database)
  - JavaScript (DOM Manipulation, Bootstrap Modal & Toast)

---

## 2. Deskripsi Aplikasi
Aplikasi **To-Do List** ini merupakan sistem pencatatan tugas sederhana berbasis web.  
Fitur utama meliputi:
- Menambahkan tugas baru dengan prioritas dan tanggal jatuh tempo.
- Menandai tugas selesai / belum selesai.
- Mengedit tugas melalui **modal popup** dengan UX intuitif.
- Menghapus tugas secara individual maupun massal.
- Filter tugas berdasarkan status (aktif/selesai).
- Pencarian instan dan pengurutan (sortir) berdasarkan judul, prioritas, tanggal, atau waktu pembuatan.
- Tampilan progress bar otomatis.
- Notifikasi real-time menggunakan **Bootstrap Toast**.

---

## 3. Cara Instalasi & Menjalankan
1. **Siapkan server lokal**  
   Gunakan XAMPP atau MAMP (wajib ada Apache + PHP).  

2. **Letakkan project di folder htdocs**  
   - Untuk XAMPP: `/Applications/XAMPP/xamppfiles/htdocs/todolist`  
   - Untuk MAMP: `/Applications/MAMP/htdocs/todolist`

3. **Jalankan Apache**  
   - Buka XAMPP/MAMP Control Panel → Start **Apache**.  

4. **Akses melalui browser**  
   - XAMPP: `http://localhost/todolist`  
   - MAMP (default port 8888): `http://localhost:8888/todolist`

---

## 4. Struktur Direktori
todolist/
├── index.php # File utama aplikasi
├── assets/
│ └── style.css # Styling tambahan
└── README.md # Dokumentasi aplikasi


---

## 5. Panduan Penggunaan
1. **Tambah Tugas**  
   Isi judul tugas → pilih prioritas → pilih jatuh tempo (opsional) → klik **Tambah**.
2. **Tandai Selesai**  
   Klik **checkbox** di samping tugas untuk mengubah status.
3. **Edit Tugas**  
   Klik ikon **pensil** → muncul modal → ubah data → **Simpan Perubahan**.
4. **Hapus Tugas**  
   Klik ikon **tempat sampah** → konfirmasi hapus.
5. **Bulk Actions**  
   - **Centang semua selesai**: klik ikon kotak centang di toolbar.  
   - **Hapus semua yang selesai**: klik ikon tempat sampah di toolbar.  
6. **Filter & Sortir**  
   - Pilih kategori (Semua, Aktif, Selesai).
   - Gunakan menu dropdown untuk urutan (judul, prioritas, tanggal, terbaru).

---

## 6. Pengujian & Debugging
- Uji setiap fitur dengan beberapa skenario:
  - Menambah tugas tanpa judul → validasi gagal.
  - Mengedit tugas dengan status dan prioritas berbeda.
  - Menghapus data lalu memastikan progres bar berubah.
- Gunakan `console.log()` (JS) dan `var_dump()` (PHP) untuk debugging.
- Semua input disanitasi dengan `htmlspecialchars()`.

---

## 7. Pemeliharaan
- **Backup**: karena data disimpan di `$_SESSION`, aplikasi ini hanya untuk simulasi ujian.  
- **Pengembangan Lanjut**:
  - Integrasi dengan MySQL/MariaDB untuk penyimpanan permanen.
  - Penambahan fitur autentikasi (login/logout).
  - Export tugas ke PDF/CSV.
  - Deploy ke hosting/VM agar bisa diakses multi-user.

---

## 8. Lisensi
Dibuat untuk keperluan **Uji Kompetensi Nasional SKKNI – Skema Junior Web Programmer**.  
Boleh dikembangkan lebih lanjut untuk pembelajaran.
