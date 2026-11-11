<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuaraMuhammadiyahTest extends TestCase
{
    use RefreshDatabase;

    private function employee(): User
    {
        return User::factory()->create([
            'role' => 'employee',
            'portal' => 'sm',
        ]);
    }

    public function test_index_redirects_to_dashboard(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.index'))
            ->assertRedirect(route('sm.dashboard'));
    }

    public function test_dashboard_is_displayed(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.dashboard'))
            ->assertOk()
            ->assertSee('Total Jenis Produk')
            ->assertSee('98.026.160');
    }

    public function test_katalog_is_displayed_with_filters(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.katalog', ['kategori' => 'KEMEJA']))
            ->assertOk()
            ->assertSee('IPM NAS SD PUTRA')
            ->assertDontSee('HW BAJU PUTRA');
    }

    public function test_kasir_is_displayed(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.kasir'))
            ->assertOk()
            ->assertSee('Keranjang');
    }

    public function test_stok_is_displayed(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.stok', ['kategori' => 'KAIN']))
            ->assertOk()
            ->assertSee('Rincian Stok');
    }

    public function test_laporan_is_displayed(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.laporan'))
            ->assertOk()
            ->assertSee('Rekapitulasi Produk');
    }

    public function test_scan_finds_product_by_code(): void
    {
        $this->actingAs($this->employee())
            ->get(route('sm.scan', ['q' => 'BRG-0002']))
            ->assertOk()
            ->assertSee('IPM NAS SD PUTRA');
    }

    public function test_portal_rejects_other_employee(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'portal' => 'qmas',
        ]);

        $this->actingAs($user)
            ->get(route('sm.dashboard'))
            ->assertForbidden();
    }
}
