<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenggunaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_membuka_halaman_pengguna(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('pengguna.index'))
            ->assertOk()
            ->assertSee($admin->email);
    }

    public function test_peran_selain_admin_ditolak_403(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($dosen)
            ->get(route('pengguna.index'))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('pengguna.index'))
            ->assertRedirect(route('login'));
    }

    public function test_halaman_internal_mengirim_header_no_store(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0');
    }

    public function test_tampilan_403_memakai_layout_internal(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($dosen)
            ->get(route('pengguna.index'))
            ->assertForbidden()
            ->assertSee('Kembali ke Dashboard');
    }
}
