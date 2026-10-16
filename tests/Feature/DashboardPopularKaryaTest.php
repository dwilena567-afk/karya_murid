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

    public function test_dashboard_lists_the_users_most_purchased_karya_for_the_selected_period(): void
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
            ->assertSee('Karya Paling Populer (7 Minggu Terakhir)')
            ->assertSeeInOrder(['Karya Terlaris', 'Karya Kedua'])
            ->assertDontSee('Karya Lama')
            ->assertDontSee('Karya Pending')
            ->assertDontSee('Karya Pengguna Lain');

        $this->get(route('dashboard.index', ['periode' => 'all_time']))
            ->assertOk()
            ->assertSee('Karya Paling Populer (Sepanjang Waktu)')
            ->assertSeeInOrder(['Karya Lama', 'Karya Terlaris']);
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
