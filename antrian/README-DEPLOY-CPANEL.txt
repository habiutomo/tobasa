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
   - Klik "Upload" lalu pilih file "antrian-deploy.zip".
   - Setelah upload selesai, KLIK KANAN file zip > "Extract".
   - Pastikan hasil ekstraksi adalah folder "antrian/" di dalam public_html.
     (Jika terekstrak sebagai banyak file langsung di public_html, pindahkan
      semua file & folder ke dalam folder antrian/ terlebih dahulu)
   - Hapus file antrian-deploy.zip setelah ekstrak (opsional).

ISI DATABASE PADA .env
----------------------
4. Buka folder antrian/, klik kanan file ".env" > "Edit".
   Ganti 3 baris berikut dengan data MySQL cPanel Anda:
     DB_DATABASE=GANTI_NAMA_DATABASE
     DB_USERNAME=GANTI_USER_DATABASE
     DB_PASSWORD=GANTI_PASSWORD_DATABASE
   (Contoh: DB_DATABASE=tobasa_antrian)
   Klik "Save". PERHATIAN: jangan ganti APP_KEY.

PERMSISI FOLDER (PENTING)
-------------------------
5. Pastikan folder berikut bisa ditulis (edit/rename alias permission 755, atau 775/777 jika perlu):
   - antrian/storage  (semua isi termasuk framework/ dan logs/)
   - antrian/bootstrap/cache
   Caranya di File Manager: klik kanan folder > "Permissions" > centang semua
   "write" untuk owner/group (misal 775). Untuk storage/framework/views bisa 777 bila perlu.

MIGRASI DATABASE (BUAT TABEL)
-----------------------------
6. Jalankan migrasi database. Pilih salah satu cara:

   Cara A (cPanel Terminal - jika tersedia):
     - cPanel > Terminal, lalu ketik:
         cd ~/public_html/antrian
         php artisan migrate --force --seed
     - Opsi "--seed" dipakai HANYA saat database masih kosong, untuk membuat
       data awal (klaster, layanan, loket, dan akun user).

   Cara B (installer via web - SEGERA HAPUS setelah selesai):
     - Buka di browser: https://tobasa.elnusaintegrated.cloud/antrian/migrate?token=BACKUP_TOKEN
       (file khusus ini ada di routes/web.php; abaikan jika tidak tersedia)

     CATATAN: Jika Cara B disertakan, hapus rute migrate tersebut dari
     routes/web.php lalu upload ulang setelah selesai.

   Cara C (jalankan dari komputer lokal melalui akses DB publik - TIDAK disarankan).

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
   - Admin   : admin@puskesmas.test  / password
   - Loket 1: loket1@puskesmas.test  / password
   - Loket 2 .. Loket 5 serupa: loketN@puskesmas.test / password

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
- Jika yang aktif justru situs lain di root domain, itu normal karena aplikasi
  berjalan di subfolder /antrian.