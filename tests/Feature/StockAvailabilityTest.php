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

    public function test_free_karya_checkout_completes_without_midtrans_and_reduces_stock(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 3, harga: 0);
        Keranjang::create([
            'user_id' => $user->id,
            'karya_id' => $karya->id,
            'jumlah' => 2,
        ]);

        $this->actingAs($user)
            ->post(route('transaksi.store'))
            ->assertRedirect(route('transaksi.show', Transaksi::first()));

        $transaksi = Transaksi::firstOrFail();
        $this->assertSame('settlement', $transaksi->transaction_status);
        $this->assertSame('free', $transaksi->payment_type);
        $this->assertSame(0.0, (float) $transaksi->gross_amount);
        $this->assertNull($transaksi->snap_token);
        $this->assertSame(1, (int) $karya->fresh()->stok);
        $this->assertDatabaseCount('keranjangs', 0);
        $this->assertDatabaseHas('transaksi_details', [
            'transaksi_id' => $transaksi->id,
            'karya_id' => $karya->id,
            'harga_satuan' => 0,
            'jumlah' => 2,
        ]);

        $this->get(route('transaksi.show', $transaksi))
            ->assertOk()
            ->assertSee('Gratis')
            ->assertDontSee('Bayar Sekarang');
    }

    public function test_pending_transaction_cannot_be_paid_when_current_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 0);
        $pending = $this->createTransaction($user, $karya, 'pending', 'snap-token', 'PENDING-ORDER');

        $response = $this->actingAs($user)->get(route('transaksi.show', $pending));

        $response->assertOk()
            ->assertSee('Stok karya pada transaksi ini tidak lagi mencukupi.')
            ->assertDontSee('Bayar Sekarang');

        $this->actingAs($user)
            ->post(route('transaksi.confirm-payment', $pending))
            ->assertStatus(409)
            ->assertJson([
                'message' => 'Stok karya pada transaksi ini tidak lagi mencukupi.',
            ]);
    }

    public function test_restocked_karya_is_payable_again_in_a_pending_transaction(): void
    {
        $user = User::factory()->create();
        $karya = $this->createKarya(stok: 0);
        $this->createTransaction($user, $karya, 'settlement', null, 'PAID-RESTOCK-ORDER');
        $karya->update(['stok' => 1]);
        $pending = $this->createTransaction($user, $karya, 'pending', 'snap-token', 'PENDING-RESTOCK-ORDER');

        $response = $this->actingAs($user)->get(route('transaksi.show', $pending));

        $response->assertOk()
            ->assertSee('Bayar Sekarang')
            ->assertDontSee('Stok karya pada transaksi ini tidak lagi mencukupi.');
    }

    public function test_deleting_karya_cancels_pending_checkout_and_removes_payment_button(): void
    {
        $owner = User::factory()->create(['role' => 'murid']);
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $karya = $this->createKarya(stok: 2);
        $karya->update(['user_id' => $owner->id]);
        $transaksi = $this->createTransaction($buyer, $karya, 'pending', 'snap-token', 'PENDING-DELETED-WORK');

        $this->actingAs($owner)
            ->delete(route('karyamu.destroy', $karya))
            ->assertRedirect(route('karyamu.index'));

        $transaksi->refresh();
        $this->assertSame('cancel', $transaksi->transaction_status);
        $this->assertSame('item_deleted', $transaksi->payment_type);
        $this->assertDatabaseHas('transaksi_details', [
            'transaksi_id' => $transaksi->id,
            'karya_id' => null,
            'harga_satuan' => 10000,
            'jumlah' => 1,
        ]);

        $this->actingAs($buyer)
            ->get(route('transaksi.show', $transaksi))
            ->assertOk()
            ->assertSee('Karya pada transaksi ini sudah dihapus. Pembayaran tidak tersedia.')
            ->assertDontSee('Bayar Sekarang');

        $this->post(route('transaksi.confirm-payment', $transaksi))
            ->assertStatus(409)
            ->assertJson([
                'message' => 'Karya pada transaksi ini sudah dihapus. Pembayaran tidak tersedia.',
            ]);
    }

    private function createKarya(int $stok, int $harga = 10000): Karya
    {
        $pembuat = User::factory()->create();
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);

        return Karya::create([
            'user_id' => $pembuat->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'harga' => $harga,
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
