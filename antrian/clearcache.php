<?php
// ============================================================
//  CLEAR CACHE MANUAL - untuk hosting SHARED tanpa terminal
// ------------------------------------------------------------
//  1) Upload file ini sampai ke folder root aplikasi antrian
//     (folder yang sama dengan file artisan), contoh:
//     public_html/antrian/clearcache.php
//  2) Buka di browser:
//     https://tobasa.elnusaintegrated.cloud/antrian/clearcache.php?token=GANTI_TOKEN
//  3) Selesai, lalu HAPUS file ini dari server setelah dipakai!
// ============================================================

header('Content-Type: text/plain; charset=utf-8');

$token = 'GANTI_TOKEN';   // GANTI token ini sesuai dengan yang dipakai di URL di atas
$root  = __DIR__;         // folder tempat file ini diletakkan

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(403);
    exit('Akses ditolak. Token salah.');
}

function hapusSampaiBersih(string $dir): array
{
    $removed = [];
    if (!is_dir($dir)) {
        return $removed;
    }

    foreach (glob(rtrim($dir, '/\\') . '/*') ?: [] as $file) {
        if (is_dir($file)) {
            $removed = array_merge($removed, hapusSampaiBersih($file));
            @rmdir($file);
        } else {
            @unlink($file);
            $removed[] = $file;
        }
    }

    return $removed;
}

$targets = [
    'Compiled views (storage/framework/views)' => $root . '/storage/framework/views',
    'Bootstrap cache (bootstrap/cache)'         => $root . '/bootstrap/cache',
];

$total = 0;
foreach ($targets as $label => $path) {
    $total += count(hapusSampaiBersih($path));
}

echo 'OK - Cache dibersihkan (' . $total . ' file).';