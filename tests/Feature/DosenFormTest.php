<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_membuka_form_tambah(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dosen.create'))
            ->assertOk()
            ->assertSee('Form Tambah Dosen');
    }

    public function test_validasi_gagal_dan_nilai_lama_tetap_tampil(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('dosen.store'), ['nidn' => '', 'nama' => 'Budi'])
            ->assertSessionHasErrors(['nidn', 'email'])
            ->assertSessionHasInput('nama', 'Budi');
    }

    public function test_form_ubah_terisi_data_lama(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dosen = Dosen::factory()->create();

        $this->actingAs($admin)
            ->get(route('dosen.edit', $dosen))
            ->assertOk()
            ->assertSee($dosen->nidn)
            ->assertSee($dosen->nama);
    }
}
