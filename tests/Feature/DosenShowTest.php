<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_dosen_menampilkan_data_lengkap(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = Dosen::factory()->create();

        $this->actingAs($admin)
            ->get(route('dosen.show', $dosen))
            ->assertOk()
            ->assertSee($dosen->nidn)
            ->assertSee($dosen->email);
    }

    public function test_id_tidak_ada_menghasilkan_404(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/dosen/9999')
            ->assertNotFound();
    }
}
