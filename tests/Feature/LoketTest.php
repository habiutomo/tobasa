<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Poli;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoketTest extends TestCase
{
    use RefreshDatabase;

    private Poli $poli;

    private Loket $loket;

    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->poli = Poli::create(['nama' => 'Pendaftaran', 'prefix' => 'A']);
        $this->loket = Loket::create(['nama' => 'Loket 1', 'poli_id' => $this->poli->id]);
        $this->petugas = User::factory()->create([
            'role' => 'loket',
            'all_clusters' => false,
            'loket_id' => $this->loket->id,
        ]);
    }

    private function antrian(int $nomor, array $extra = []): Antrian
    {
        return Antrian::create(array_merge([
            'tanggal' => today()->toDateString(),
            'poli_id' => $this->poli->id,
            'nomor' => $nomor,
            'no_antrian' => 'A'.str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
            'nama_pasien' => 'Pasien '.$nomor,
            'status' => 'waiting',
        ], $extra));
    }

    public function test_halaman_loket_menampilkan_nomor_yang_sedang_dipanggil(): void
    {
        $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);

        $this->actingAs($this->petugas)
            ->get('/loket')
            ->assertOk()
            ->assertSee('A001')
            ->assertSee('Pasien 1');
    }

    public function test_panggil_mengambil_nomor_terkecil(): void
    {
        $this->antrian(2);
        $pertama = $this->antrian(1);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil')
            ->assertOk()
            ->assertJsonPath('dipanggil', true)
            ->assertJsonPath('antrian.no_antrian', 'A001');

        $this->assertDatabaseHas('antrians', [
            'id' => $pertama->id,
            'status' => 'calling',
            'loket_id' => $this->loket->id,
        ]);
    }

    public function test_panggil_spesifik_memanggil_nomor_yang_dipilih(): void
    {
        $this->antrian(1);
        $ketiga = $this->antrian(3);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil?antrian_id='.$ketiga->id)
            ->assertOk()
            ->assertJsonPath('antrian.no_antrian', 'A003');

        $this->assertDatabaseHas('antrians', ['id' => $ketiga->id, 'status' => 'calling']);
    }

    public function test_panggil_spesifik_ditolak_bila_antrian_bukan_milik_poli_loket(): void
    {
        $poliLain = Poli::create(['nama' => 'KIA', 'prefix' => 'C']);
        $antrianLain = Antrian::create([
            'tanggal' => today()->toDateString(),
            'poli_id' => $poliLain->id,
            'nomor' => 1,
            'no_antrian' => 'C001',
            'status' => 'waiting',
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil?antrian_id='.$antrianLain->id)
            ->assertNotFound();

        $this->assertDatabaseHas('antrians', ['id' => $antrianLain->id, 'status' => 'waiting']);
    }

    public function test_panggil_saat_tidak_ada_antrian_mengembalikan_404(): void
    {
        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil')
            ->assertNotFound();
    }

    public function test_memanggil_berikutnya_menandai_nomor_sebelumnya_sebagai_dilewat(): void
    {
        $sebelumnya = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);
        $this->antrian(2);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil')
            ->assertOk()
            ->assertJsonPath('antrian.no_antrian', 'A002');

        $this->assertDatabaseHas('antrians', [
            'id' => $sebelumnya->id,
            'status' => 'skipped',
            'finished_at' => $sebelumnya->fresh()->finished_at,
        ]);
        $this->assertNotNull($sebelumnya->fresh()->skipped_at);
    }

    public function test_selesai_menandai_nomor_berstatus_done(): void
    {
        $antrian = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/selesai')
            ->assertOk()
            ->assertJsonPath('message', 'A001 selesai dilayani.');

        $this->assertDatabaseHas('antrians', ['id' => $antrian->id, 'status' => 'done']);
        $this->assertNotNull($antrian->fresh()->finished_at);
    }

    public function test_selesai_tidak_memanggil_otomatis_secara_bawaan(): void
    {
        $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);
        $berikutnya = $this->antrian(2);

        $this->actingAs($this->petugas)->postJson('/loket/selesai')->assertOk();

        $this->assertDatabaseHas('antrians', ['id' => $berikutnya->id, 'status' => 'waiting']);
    }

    public function test_selesai_memanggil_otomatis_bila_pengaturan_aktif(): void
    {
        Setting::set('auto_panggil', '1');

        $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);
        $berikutnya = $this->antrian(2);

        $this->actingAs($this->petugas)
            ->postJson('/loket/selesai')
            ->assertOk()
            ->assertJsonPath('dipanggil', true)
            ->assertJsonPath('antrian.no_antrian', 'A002');

        $this->assertDatabaseHas('antrians', [
            'id' => $berikutnya->id,
            'status' => 'calling',
            'loket_id' => $this->loket->id,
        ]);
    }

    public function test_lewati_memanggil_nomor_berikutnya(): void
    {
        $dilempar = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);
        $berikutnya = $this->antrian(2);

        $this->actingAs($this->petugas)
            ->postJson('/loket/lewati')
            ->assertOk()
            ->assertJsonPath('lewati', 'A001')
            ->assertJsonPath('antrian.no_antrian', 'A002');

        $this->assertDatabaseHas('antrians', ['id' => $dilempar->id, 'status' => 'skipped']);
        $this->assertDatabaseHas('antrians', [
            'id' => $berikutnya->id,
            'status' => 'calling',
            'loket_id' => $this->loket->id,
        ]);
    }

    public function test_lewati_tidak_menggagalkan_bila_antrian_kosong(): void
    {
        $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/lewati')
            ->assertOk()
            ->assertJsonPath('message', 'Lewati A001. Antrian kosong.')
            ->assertJsonMissingPath('antrian');
    }

    public function test_panggil_ulang_memperbarui_waktu_panggilan(): void
    {
        $antrian = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil-ulang')
            ->assertOk()
            ->assertJsonPath('dipanggil', true)
            ->assertJsonPath('antrian.no_antrian', 'A001');

        $this->assertTrue($antrian->fresh()->called_at->isAfter(now()->subMinute()));
    }

    public function test_aksi_tanpa_nomor_yang_dipanggil_mengembalikan_404(): void
    {
        $this->actingAs($this->petugas)->postJson('/loket/selesai')->assertNotFound();
        $this->actingAs($this->petugas)->postJson('/loket/lewati')->assertNotFound();
        $this->actingAs($this->petugas)->postJson('/loket/panggil-ulang')->assertNotFound();
        $this->actingAs($this->petugas)->postJson('/loket/batalkan')->assertNotFound();
    }

    public function test_batalkan_mengembalikan_nomor_ke_antrean(): void
    {
        $antrian = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/batalkan')
            ->assertOk()
            ->assertJsonMissingPath('dipanggil');

        $this->assertDatabaseHas('antrians', [
            'id' => $antrian->id,
            'status' => 'waiting',
            'loket_id' => null,
            'called_at' => null,
        ]);
    }

    public function test_kembalikan_mengembalikan_nomor_yang_dilewat(): void
    {
        $antrian = $this->antrian(1, [
            'status' => 'skipped',
            'loket_id' => $this->loket->id,
            'called_at' => now()->subMinutes(3),
            'skipped_at' => now(),
            'finished_at' => now(),
        ]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/kembalikan', ['antrian_id' => $antrian->id])
            ->assertOk();

        $this->assertDatabaseHas('antrians', [
            'id' => $antrian->id,
            'status' => 'waiting',
            'loket_id' => null,
            'skipped_at' => null,
        ]);
    }

    public function test_kembalikan_ditolak_untuk_antrian_yang_bukan_dilewat(): void
    {
        $antrian = $this->antrian(1, ['status' => 'done', 'finished_at' => now()]);

        $this->actingAs($this->petugas)
            ->postJson('/loket/kembalikan', ['antrian_id' => $antrian->id])
            ->assertNotFound();

        $this->assertDatabaseHas('antrians', ['id' => $antrian->id, 'status' => 'done']);
    }

    public function test_data_loket_menyediakan_statistik_dan_antrian_antrean(): void
    {
        $this->antrian(1, [
            'status' => 'done',
            'called_at' => now()->subMinutes(10),
            'finished_at' => now()->subMinutes(5),
        ]);
        $this->antrian(2);
        $this->antrian(3, ['nama_pasien' => 'Budi Santoso']);

        $this->actingAs($this->petugas)
            ->getJson('/loket/data')
            ->assertOk()
            ->assertJsonPath('loket.nama', 'Loket 1')
            ->assertJsonPath('waiting_count', 2)
            ->assertJsonPath('stats.total_done', 1)
            ->assertJsonCount(2, 'waiting');
    }

    public function test_pencarian_menyaring_antrean_tanpa_mengubah_total(): void
    {
        $this->antrian(1, ['nama_pasien' => 'Siti Aminah']);
        $this->antrian(2, ['nama_pasien' => 'Budi Santoso']);

        $this->actingAs($this->petugas)
            ->getJson('/loket/data?q=Budi')
            ->assertOk()
            ->assertJsonPath('waiting_count', 2)
            ->assertJsonPath('waiting_matched', 1)
            ->assertJsonPath('waiting.0.nama_pasien', 'Budi Santoso');
    }

    public function test_nomor_dipanggil_tidak_muncul_di_antrean(): void
    {
        $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);
        $this->antrian(2);

        $this->actingAs($this->petugas)
            ->getJson('/loket/data')
            ->assertOk()
            ->assertJsonPath('waiting_count', 1)
            ->assertJsonPath('current.no_antrian', 'A001');
    }

    public function test_loket_lain_tidak_bisa_melihat_antrian_poli_lain(): void
    {
        $poliLain = Poli::create(['nama' => 'KIA', 'prefix' => 'C']);
        $loketLain = Loket::create(['nama' => 'Loket C', 'poli_id' => $poliLain->id]);
        $petugasLain = User::factory()->create([
            'role' => 'loket',
            'all_clusters' => false,
            'loket_id' => $loketLain->id,
        ]);

        $antrianLain = Antrian::create([
            'tanggal' => today()->toDateString(),
            'poli_id' => $poliLain->id,
            'nomor' => 1,
            'no_antrian' => 'C001',
            'status' => 'waiting',
        ]);
        $this->antrian(1);

        $this->actingAs($petugasLain)
            ->getJson('/loket/data')
            ->assertOk()
            ->assertJsonPath('waiting_count', 1)
            ->assertJsonPath('waiting.0.no_antrian', 'C001');

        $this->actingAs($this->petugas)
            ->getJson('/loket/data')
            ->assertOk()
            ->assertJsonPath('waiting.0.no_antrian', 'A001');

        $this->assertDatabaseHas('antrians', ['id' => $antrianLain->id, 'status' => 'waiting']);
    }

    public function test_antrian_hari_lain_tidak_ikut_terhitung(): void
    {
        Antrian::create([
            'tanggal' => today()->subDay()->toDateString(),
            'poli_id' => $this->poli->id,
            'nomor' => 1,
            'no_antrian' => 'A001',
            'status' => 'waiting',
        ]);
        $ini = $this->antrian(1);

        $this->actingAs($this->petugas)
            ->postJson('/loket/panggil')
            ->assertOk()
            ->assertJsonPath('antrian.no_antrian', 'A001');

        $this->assertDatabaseHas('antrians', ['id' => $ini->id, 'status' => 'calling']);
    }

    public function test_admin_tidak_bisa_mengakses_halaman_loket(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'all_clusters' => false, 'loket_id' => null]);

        $this->actingAs($admin)->get('/loket')->assertForbidden();
    }

    public function test_nomor_dipanggil_tidak_bisa_dilayani_loket_lain(): void
    {
        $antrian = $this->antrian(1, [
            'status' => 'calling',
            'loket_id' => $this->loket->id,
            'called_at' => now(),
        ]);

        $this->actingAs($this->petugas)->postJson('/loket/selesai')->assertOk();

        $this->assertDatabaseHas('antrians', ['id' => $antrian->id, 'status' => 'done']);
    }

    public function test_halaman_loket_menampilkan_shortcut_dan_kontrol(): void
    {
        $this->actingAs($this->petugas)
            ->get('/loket')
            ->assertOk()
            ->assertSee('Panggil Berikutnya')
            ->assertSee('Pintasan Keyboard')
            ->assertSee('Cari nomor')
            ->assertSee('Batalkan Panggilan');
    }
}
