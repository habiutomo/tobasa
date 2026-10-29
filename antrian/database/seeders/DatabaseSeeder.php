<?php

namespace Database\Seeders;

use App\Models\Klaster;
use App\Models\Loket;
use App\Models\Poli;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Setting::set('nama_puskesmas', 'PUSKESMAS KECAMATAN LESUNG BATU');
        Setting::set('marquee_text', 'Selamat Datang di Pelayanan Kesehatan Puskesmas. Mohon menunggu nomor antrian Anda dipanggil sesuai klaster.');
        Setting::set('kiosk_open', '1');
        Setting::set('jam_buka', '08:00');
        Setting::set('jam_tutup', '14:00');

        $klasters = [
            ['nama' => 'Klaster 1 (Administrasi)', 'warna' => '#0d6efd', 'urutan' => 1],
            ['nama' => 'Klaster 2 (KIA, Remaja, Imunisasi)', 'warna' => '#d63384', 'urutan' => 2],
            ['nama' => 'Klaster 3 (Usia Dewasa dan Lansia)', 'warna' => '#198754', 'urutan' => 3],
            ['nama' => 'Klaster 4 (Penyakit Menular)', 'warna' => '#fd7e14', 'urutan' => 4],
            ['nama' => 'Lintas Klaster (Gimul, Apotik, IGD, Laboratorium)', 'warna' => '#6f42c1', 'urutan' => 5],
        ];

        foreach ($klasters as $k) {
            Klaster::create($k);
        }

        $data = [
            // Klaster 1 : Administrasi
            ['klaster' => 1, 'nama' => 'Pendaftaran', 'prefix' => 'A', 'warna' => '#0d6efd', 'deskripsi' => 'Loket administrasi & pendaftaran pasien'],
            ['klaster' => 1, 'nama' => 'Administrasi', 'prefix' => 'B', 'warna' => '#3d8bfd', 'deskripsi' => 'Pelayanan administrasi lainnya'],

            // Klaster 2 : KIA, Remaja, Imunisasi
            ['klaster' => 2, 'nama' => 'KIA', 'prefix' => 'C', 'warna' => '#d63384', 'deskripsi' => 'Kesehatan Ibu dan Anak'],
            ['klaster' => 2, 'nama' => 'Remaja', 'prefix' => 'D', 'warna' => '#e86aa1', 'deskripsi' => 'Pelayanan kesehatan remaja'],
            ['klaster' => 2, 'nama' => 'Imunisasi', 'prefix' => 'E', 'warna' => '#f0a0c6', 'deskripsi' => 'Pelayanan imunisasi'],

            // Klaster 3 : Usia dewasa dan lansia
            ['klaster' => 3, 'nama' => 'Umum Dewasa', 'prefix' => 'F', 'warna' => '#198754', 'deskripsi' => 'Pelayanan penyakit umum dewasa'],
            ['klaster' => 3, 'nama' => 'Lansia', 'prefix' => 'G', 'warna' => '#41b57b', 'deskripsi' => 'Pelayanan kesehatan lansia'],

            // Klaster 4 : Penyakit Menular
            ['klaster' => 4, 'nama' => 'TB & Paru', 'prefix' => 'H', 'warna' => '#fd7e14', 'deskripsi' => 'Pelayanan Tuberkulosis (TB)'],
            ['klaster' => 4, 'nama' => 'DBD & Demam', 'prefix' => 'I', 'warna' => '#fea84e', 'deskripsi' => 'Pelayanan Demam Berdarah & demam'],

            // Lintas Klaster : Gimul, Apotik, IGD, Laboratorium
            ['klaster' => 5, 'nama' => 'Gimul', 'prefix' => 'J', 'warna' => '#6f42c1', 'deskripsi' => 'Gigi dan Mulut'],
            ['klaster' => 5, 'nama' => 'Apotik', 'prefix' => 'K', 'warna' => '#8a63d2', 'deskripsi' => 'Pengambilan obat'],
            ['klaster' => 5, 'nama' => 'IGD', 'prefix' => 'L', 'warna' => '#a98edc', 'deskripsi' => 'Instalasi Gawat Darurat'],
            ['klaster' => 5, 'nama' => 'Laboratorium', 'prefix' => 'M', 'warna' => '#c2afe8', 'deskripsi' => 'Pelayanan laboratorium'],
        ];

        foreach ($data as $p) {
            Poli::create([
                'klaster_id' => $p['klaster'],
                'nama' => $p['nama'],
                'prefix' => $p['prefix'],
                'warna' => $p['warna'],
                'deskripsi' => $p['deskripsi'],
            ]);
        }

        // Setiap poli aktif wajib punya loket, kalau tidak tiketnya tidak
        // akan pernah muncul di Loket Panggilan maupun Display.
        $urut = Loket::count();

        foreach (Poli::where('is_active', true)->orderBy('prefix')->get() as $poli) {
            Loket::create([
                'nama' => 'Loket '.(++$urut).' - '.$poli->nama,
                'poli_id' => $poli->id,
            ]);
        }

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@puskesmas.test',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Petugas Loket',
            'email' => 'loket1@puskesmas.test',
            'role' => 'loket',
            'loket_id' => null,
            'all_clusters' => true,
            'password' => bcrypt('password'),
        ]);
    }
}
