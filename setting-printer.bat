@echo off
title Pengaturan Printer Struk Antrian (80 x 50 mm)
chcp 65001 >nul
setlocal enabledelayedexpansion

rem Buka dialog 'Print Server Properties' > tab Forms
rem (buat form baru 80 x 50 mm lalu Save Form)
start "" rundll32 printui.dll,PrintUIEntry /s

rem Cari printer thermal, kalau tidak ada pakai printer default
set "PRN="
for /f "usebackq delims=" %%p in (`powershell -NoProfile -Command "$p = Get-CimInstance Win32_Printer | Where-Object { $_.Name -match 'thermal|receipt|struk|80mm|58mm|esc|tm-|pos|kiosk' }; if ($p) { ($p | Select-Object -First 1).Name } else { (Get-CimInstance Win32_Printer -Filter 'Default=True' | Select-Object -First 1).Name }"`) do set "PRN=%%p"

if not defined PRN (
    echo Printer tidak terdeteksi.
    exit /b
)

rem Buka Printing Preferences printer tersebut
start "" rundll32 printui.dll,PrintUIEntry /e /n "%PRN%"

echo Printer: %PRN%
echo Atur di jendela yang terbuka:
echo   - Paper Size: Struk 80x50  (80 x 50 mm)
echo   - Orientasi : Portrait
echo   - Margin    : None