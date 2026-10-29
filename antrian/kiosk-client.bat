@echo off
title Antrian Tobasa - Kiosk Ambil Antrian
rem ============================================
rem   KIOSK CLIENT - Ambil Nomor Antrian
rem   Server: tobasa.elnusaintegrated.cloud
rem   - Mode kiosk fullscreen
rem   - --kiosk-printing : cetak otomatis TANPA print preview
rem   - Profil khusus kiosk : memastikan flag selalu aktif
rem============================================

set "URL=http://tobasa.elnusaintegrated.cloud/kiosk"
set "PROFILE=%TEMP%\AntrianKioskProfile"

echo ========================================
echo   KIOSK ANTRIAN - TOBASA
echo   Membuka: %URL%
echo ========================================
echo.

rem Tutup instance kiosk lama (hanya yang pakai profil AntrianKiosk,
rem tidak menyentuh browsing normal)
for /f "usebackq tokens=*" %%p in (`powershell -NoProfile -Command "Get-CimInstance Win32_Process | Where-Object { $_.Name -in @('msedge.exe','chrome.exe') -and $_.CommandLine -like '*AntrianKioskProfile*' } | Select-Object -ExpandProperty ProcessId"`) do (
    taskkill /PID %%p /F >nul 2>&1
)
timeout /t 1 /nobreak >nul

rem Coba Microsoft Edge (mode kiosk)
set "EDGE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if exist "%EDGE%" (
    start "" "%EDGE%" --kiosk "%URL%" --edge-kiosk-type=fullscreen --kiosk-printing --no-first-run --no-default-browser-check "--user-data-dir=%PROFILE%"
    goto :done
)

rem Coba Google Chrome (mode kiosk)
set "CHROME=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if exist "%CHROME%" (
    start "" "%CHROME%" --kiosk "%URL%" --kiosk-printing --no-first-run --no-default-browser-check "--user-data-dir=%PROFILE%"
    goto :done
)

set "CHROME_X86=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
if exist "%CHROME_X86%" (
    start "" "%CHROME_X86%" --kiosk "%URL%" --kiosk-printing --no-first-run --no-default-browser-check "--user-data-dir=%PROFILE%"
    goto :done
)

rem Fallback: browser default
start "" "%URL%"

:done
echo Kiosk dibuka. Keluar tampilan penuh dengan ALT+F4 atau F11.
echo.
pause