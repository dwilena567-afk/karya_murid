<?php

namespace Tests\Feature;

use App\Models\Karya;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPopularKaryaTest extends TestCase
{
    use RefreshDatabase;

    public function test_popular_karya_period_is_independent_from_the_sales_trend_period(): void
    {
        $owner = User::factory()->create(['role' => 'murid']);
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $best = $this->createKarya($owner, 'Karya Terlaris');
        $second = $this->createKarya($owner, 'Karya Kedua');
        $old = $this->createKarya($owner, 'Karya Lama');
        $pending = $this->createKarya($owner, 'Karya Pending');
        $otherOwnerKarya = $this->createKarya(User::factory()->create(['role' => 'murid']), 'Karya Pengguna Lain');

        $this->recordPurchase($buyer, $best, 4);
        $this->recordPurchase($buyer, $second, 2);
        $this->recordPurchase($buyer, $old, 9, createdAt: now()->subWeeks(8));
        $this->recordPurchase($buyer, $pending, 50, status: 'pending');
        $this->recordPurchase($buyer, $otherOwnerKarya, 100);

        $this->actingAs($owner)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Tren Penjualan (7 Minggu Terakhir)')
            ->assertSee('Karya Paling Populer (7 Minggu Terakhir)')
            ->assertSeeInOrder(['Karya Terlaris', 'Karya Kedua'])
            ->assertDontSee('Karya Lama')
            ->assertDontSee('Karya Pending')
            ->assertDontSee('Karya Pengguna Lain');

        $this->get(route('dashboard.index', ['periode' => 'all_time']))
            ->assertOk()
            ->assertSee('Tren Penjualan (Sepanjang Waktu)')
            ->assertSee('Karya Paling Populer (7 Minggu Terakhir)')
            ->assertDontSee('Karya Lama');

        $this->get(route('dashboard.index', ['periode_populer' => 'all_time']))
            ->assertOk()
            ->assertSee('Tren Penjualan (7 Minggu Terakhir)')
            ->assertSee('Karya Paling Populer (Sepanjang Waktu)')
            ->assertSeeInOrder(['Karya Lama', 'Karya Terlaris']);

        $this->get(route('dashboard.index', ['periode' => 'all_time', 'periode_populer' => '1_bulan']))
            ->assertOk()
            ->assertSee(route('dashboard.index', ['periode' => '7_minggu', 'periode_populer' => '1_bulan']))
            ->assertSee(route('dashboard.index', ['periode' => 'all_time', 'periode_populer' => '7_minggu']))
            ->assertSee('Rentang tanggal:');
    }

    public function test_dashboard_shows_helpful_actions_when_there_are_no_sales(): void
    {
        $owner = User::factory()->create(['role' => 'murid']);

        $this->actingAs($owner)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Belum ada penjualan pada rentang tanggal ini.')
            ->assertSee('Belum ada karya terjual pada rentang tanggal ini.')
            ->assertSee('Coba rentang tanggal lain, atau kelola dan promosikan karyamu agar mulai ditemukan pembeli.')
            ->assertSee(route('karyamu.index'));
    }

    private function createKarya(User $owner, string $judul): Karya
    {
        return Karya::create([
            'user_id' => $owner->id,
            'judul' => $judul,
            'deskripsi' => 'Deskripsi karya.',
            'kategori_id' => Kategori::create(['nama' => 'Kategori ' . $judul])->id,
            'harga' => 10000,
            'stok' => 10,
            'status_verifikasi' => 'approved',
        ]);
    }

    private function recordPurchase(
        User $buyer,
        Karya $karya,
        int $quantity,
        string $status = 'settlement',
        ?CarbonInterface $createdAt = null
    ): void {
        $transaksi = Transaksi::create([
            'order_id' => fake()->unique()->uuid(),
            'user_id' => $buyer->id,
            'gross_amount' => $quantity * 10000,
            'transaction_status' => $status,
        ]);

        $detail = TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'karya_id' => $karya->id,
            'harga_satuan' => 10000,
            'jumlah' => $quantity,
        ]);

        if ($createdAt !== null) {
            $detail->forceFill(['created_at' => $createdAt])->save();
        }
    }
}
