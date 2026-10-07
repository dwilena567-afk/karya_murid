<?php

namespace Tests\Feature;

use App\Models\Karya;
use App\Models\Kategori;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class StockAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_adding_the_same_work_again_cannot_exceed_its_stock(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 1);

        $this->actingAs($user)->post(route('keranjang.store'), [
            'karya_id' => $karya->id,
            'jumlah' => 1,
        ])->assertSessionHas('success');

        $this->actingAs($user)->post(route('keranjang.store'), [
            'karya_id' => $karya->id,
            'jumlah' => 1,
        ])->assertSessionHas('error');

        $this->assertSame(1, (int) Keranjang::where('user_id', $user->id)
            ->where('karya_id', $karya->id)
            ->sum('jumlah'));
    }

    public function test_checkout_rejects_a_cart_quantity_above_available_stock(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 1);
        Keranjang::create([
            'user_id' => $user->id,
            'karya_id' => $karya->id,
            'jumlah' => 5,
        ]);

        $this->actingAs($user)
            ->post(route('transaksi.store'))
            ->assertSessionHas(
                'error',
                'Gagal memproses checkout: Stok Karya Tes tidak mencukupi. Stok tersedia: 1, jumlah di keranjang: 5.'
            );

        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseHas('keranjangs', [
            'user_id' => $user->id,
            'karya_id' => $karya->id,
            'jumlah' => 5,
        ]);
    }

    public function test_pending_transaction_cannot_be_paid_if_the_same_user_already_paid_for_a_karya(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 1);
        $paid = $this->createTransaction($user, $karya, 'settlement', null, 'PAID-ORDER');
        $pending = $this->createTransaction($user, $karya, 'pending', 'snap-token', 'PENDING-ORDER');

        $response = $this->actingAs($user)->get(route('transaksi.show', $pending));

        $response->assertOk()
            ->assertSee('Karya pada transaksi ini sudah dibayar melalui transaksi lain.')
            ->assertDontSee('Bayar Sekarang');

        $this->actingAs($user)
            ->post(route('transaksi.confirm-payment', $pending))
            ->assertStatus(409)
            ->assertJson([
                'message' => 'Karya pada transaksi ini sudah dibayar melalui transaksi lain.',
            ]);
    }

    public function test_pending_transaction_remains_payable_when_paid_transaction_contains_different_karya(): void
    {
        $user = User::factory()->create();
        $paidKarya = $this->createKarya(stok: 1);
        $pendingKarya = $this->createKarya(stok: 1);
        $this->createTransaction($user, $paidKarya, 'settlement', null, 'PAID-DIFFERENT-ORDER');
        $pending = $this->createTransaction($user, $pendingKarya, 'pending', 'snap-token', 'PENDING-DIFFERENT-ORDER');

        $response = $this->actingAs($user)->get(route('transaksi.show', $pending));

        $response->assertOk()
            ->assertSee('Bayar Sekarang')
            ->assertDontSee('Pembayaran untuk transaksi ini tidak tersedia.');
    }

    private function createKarya(int $stok): Karya
    {
        $pembuat = User::factory()->create();
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);

        return Karya::create([
            'user_id' => $pembuat->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => $stok,
            'status_verifikasi' => 'approved',
        ]);
    }

    private function createTransaction(
        User $user,
        Karya $karya,
        string $status,
        ?string $snapToken,
        string $orderId
    ): Transaksi {
        $transaksi = Transaksi::create([
            'order_id' => $orderId,
            'user_id' => $user->id,
            'gross_amount' => 10000,
            'transaction_status' => $status,
            'snap_token' => $snapToken,
        ]);

        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'karya_id' => $karya->id,
            'harga_satuan' => 10000,
            'jumlah' => 1,
        ]);

        return $transaksi;
    }
}
