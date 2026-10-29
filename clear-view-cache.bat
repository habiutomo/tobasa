@echo off
title Antrian Tobasa - Bersihkan View Cache
rem ============================================
rem   Jalankan dari folder aplikasi antrian
rem   (double-click di sini = bersihkan cache
rem   view versi lokal/development)
rem   Untuk server cPanel gunakan Terminal:
rem   cd ~/public_html/antrian && php artisan view:clear
rem ============================================
cd /d "%~dp0"
php artisan view:clear
pause