<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllClustersTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_clusters_user_can_select_and_call_another_loket(): void
    {
        $user = User::factory()->create([
            'all_clusters' => true,
            'loket_id' => null,
        ]);

        $firstPoli = Poli::create(['nama' => 'Pendaftaran', 'prefix' => 'A']);
        $firstLoket = Loket::create(['nama' => 'Loket A', 'poli_id' => $firstPoli->id]);
        $secondPoli = Poli::create(['nama' => 'KIA', 'prefix' => 'C']);
        $secondLoket = Loket::create(['nama' => 'Loket C', 'poli_id' => $secondPoli->id]);

        Antrian::create([
            'tanggal' => today(),
            'poli_id' => $secondPoli->id,
            'nomor' => 1,
            'no_antrian' => 'C-001',
            'nama_pasien' => 'Pasien KIA',
        ]);

        $this->actingAs($user)
            ->get('/loket')
            ->assertOk()
            ->assertSee('Pilih Klaster / Loket')
            ->assertSee($firstLoket->nama)
            ->assertSee($secondLoket->nama);

        $this->post('/loket/pilih', ['loket_id' => $secondLoket->id])
            ->assertRedirect('/loket');

        $this->post('/loket/panggil')
            ->assertOk()
            ->assertJsonPath('antrian.loket', $secondLoket->nama);

        $this->assertDatabaseHas('antrians', [
            'id' => 1,
            'loket_id' => $secondLoket->id,
            'status' => 'calling',
        ]);
    }

    public function test_regular_loket_user_cannot_select_another_loket(): void
    {
        $firstPoli = Poli::create(['nama' => 'Pendaftaran', 'prefix' => 'A']);
        $firstLoket = Loket::create(['nama' => 'Loket A', 'poli_id' => $firstPoli->id]);
        $secondPoli = Poli::create(['nama' => 'KIA', 'prefix' => 'C']);
        $secondLoket = Loket::create(['nama' => 'Loket C', 'poli_id' => $secondPoli->id]);
        $user = User::factory()->create([
            'loket_id' => $firstLoket->id,
            'all_clusters' => false,
        ]);

        $this->actingAs($user)
            ->post('/loket/pilih', ['loket_id' => $secondLoket->id])
            ->assertForbidden();
    }
}
