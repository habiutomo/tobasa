@echo off
title Antrian Tobasa - Display dan Loket
set "DISPLAY=https://tobasa.elnusaintegrated.cloud"
set "LOKET=https://tobasa.elnusaintegrated.cloud/loket"
start "" "%DISPLAY%"
timeout /t 1 /nobreak >nul
start "" "%LOKET%"
exit /b 0
