========================================================
DEPLOY APLIKASI ANTRIAN PUSKESMAS KE CPANEL
Target: tobasa.elnusaintegrated.cloud/antrian (subfolder public_html/antrian)
========================================================

PERSIAPAN DI CPANEL
------------------
1. MySQL Databases:
   - Pastikan database + user MySQL sudah dibuat (melalui cPanel > MySQL Databases).
   - Catat: nama database, username, password.

2. PHP Version (cPanel > Select PHP Version):
   - Pilih PHP 8.2 atau lebih tinggi.
   - Aktifkan ekstensi: pdo_mysql, mbstring, openssl, tokenizer, curl, fileinfo, intl.

3. Upload File (cPanel > File Manager):
   - Masuk ke folder "public_html".
    - Klik "Upload" lalu pilih file "antrian-release-cpanel.zip".
    - Setelah upload selesai, KLIK KANAN file zip > "Extract".
    - Pastikan hasil ekstraksi adalah folder "antrian/" di dalam public_html.
      (Jika terekstrak sebagai banyak file langsung di public_html, pindahkan
       semua file & folder ke dalam folder antrian/ terlebih dahulu)
    - Hapus file antrian-release-cpanel.zip setelah ekstrak (opsional).

ISI DATABASE PADA .env
----------------------
4. Di folder antrian/, salin file "env.production.example" menjadi ".env".
   Klik kanan ".env" > "Edit", lalu ganti nilai berikut:
     DB_DATABASE=GANTI_NAMA_DATABASE
     DB_USERNAME=GANTI_USER_DATABASE
     DB_PASSWORD=GANTI_PASSWORD_DATABASE
     APP_KEY=GANTI_APP_KEY_DARI_LOKAL
   (Contoh DB: tobasa_antrian)
   APP_KEY dapat dibuat di komputer lokal dengan:
     php -r "echo 'base64:'.base64_encode(random_bytes(32)), PHP_EOL;"
   Jangan pernah memakai APP_KEY contoh atau mengirimkannya ke chat.

PERMSISI FOLDER (PENTING)
-------------------------
5. Pastikan folder berikut bisa ditulis (edit/rename alias permission 755, atau 775/777 jika perlu):
   - antrian/storage  (semua isi termasuk framework/ dan logs/)
   - antrian/bootstrap/cache
   Caranya di File Manager: klik kanan folder > "Permissions" > centang semua
   "write" untuk owner/group (misal 775). Untuk storage/framework/views bisa 777 bila perlu.

MIGRASI DATABASE (BUAT TABEL)
-----------------------------
6. Import SQL melalui phpMyAdmin:
   - Database baru: import "antrian-reset.sql" atau "antrian-empty.sql".
   - Database lama: import "antrian-all-clusters.sql" untuk menambahkan kolom
     akses semua klaster dan mengubah akun loket1 menjadi akun bersama.
   - "antrian-reset.sql" menghapus tabel lama; jangan import ke database
     yang bukan khusus aplikasi Antrian.
   - Jika memakai reset, akun loket tunggal sudah tersedia dan tidak perlu
     menjalankan migrate dari Terminal.

OPTIMALISASI
------------
7. (Opsional, hanya jika cPanel Terminal tersedia)
     cd ~/public_html/antrian
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     php artisan storage:link

SELESAI & UJI
-------------
8. Buka di browser:
   - https://tobasa.elnusaintegrated.cloud/antrian           (layar antrian/panggilan)
   - https://tobasa.elnusaintegrated.cloud/antrian/kiosk     (mesin ambil nomor)
   - https://tobasa.elnusaintegrated.cloud/antrian/login     (panel loket & admin)

9. Akun default hasil seeder (PENTING: ganti password segera):
   - Admin: admin@puskesmas.test  / password
   - Petugas shared: loket1@puskesmas.test  / password
     Petugas ini dapat memilih semua loket/klaster aktif dari panel Loket.

10. Pastikan joomla/cpanel server tidak memblokir:
   - Tata tertib SEO: pastikan situs di domain root tidak menimpa /antrian.

TROUBLESHOOTING
---------------
- Halaman 403 / Internal Server Error:
  * periksa permission storage/ (langkah 5).
  * pastikan PHP >= 8.2 dipilih.
  * jika .htaccess bermasalah, pastikan mod_rewrite aktif untuk subdomain.
- Halaman 500: buka file antrian/storage/logs/laravel.log di File Manager,
  atau cPanel > Error Logs.
- Link/asset tidak lengkap "/antrian": pastikan baris
  APP_URL=https://tobasa.elnusaintegrated.cloud/antrian ada di .env.

API PANGGILAN ANTRIAN (v2)
--------------------------
Aplikasi menyediakan API JSON untuk memanggil antrian dari perangkat lain
(komputer kasir, HP petugas, aplikasi pihak ketiga). Semua endpoint wajib
menyertakan Token API petugas loket yang dibuat di Menu Admin > Petugas
(tombol "Buat Token"). Token dikirim lewat header:

     Authorization: Bearer <TOKEN>

   Untuk petugas shared, setiap request dapat memilih loket dengan:
     X-Loket-Id: <ID_LOKET>
   atau parameter loket_id pada URL/body.

Endpoint (prefix: https://tobasa.elnusaintegrated.cloud/antrian/api/q):
  GET  /status         Status: nomor sedang dipanggil + daftar antrian menunggu
  POST /panggil        Memanggil nomor berikutnya
  POST /panggil-ulang  Memanggil ulang nomor yang sedang dipanggil
  POST /selesai        Menandai pasien selesai dilayani
  POST /lewati         Melewati nomor dan beralih ke berikutnya

Contoh (PowerShell / curl):
  curl -H "Authorization: Bearer TOKEN" https://tobasa.../antrian/api/q/status
  curl -X POST -H "Authorization: Bearer TOKEN" https://tobasa.../antrian/api/q/panggil

PENTING: database baru sudah memiliki kolom API dan akses semua klaster.
Untuk database lama, import "antrian-all-clusters.sql" melalui phpMyAdmin
setelah mengunggah paket terbaru.

Untuk memudahkan uji coba dari browser, token juga bisa dikirim via parameter
URL: /antrian/api/q/status?api_token=TOKEN
- Jika yang aktif justru situs lain di root domain, itu normal karena aplikasi
  berjalan di subfolder /antrian.