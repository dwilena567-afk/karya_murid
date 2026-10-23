<?php

namespace Tests\Feature;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionHistoryPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_history_shows_seven_transactions_per_page(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 8) as $number) {
            Transaksi::create([
                'order_id' => 'ORDER-' . $number,
                'user_id' => $user->id,
                'gross_amount' => 1000,
                'transaction_status' => 'settlement',
                'created_at' => now()->subMinutes($number),
                'updated_at' => now()->subMinutes($number),
            ]);
        }

        $response = $this->actingAs($user)->get(route('transaksi.index'));

        $response->assertOk()
            ->assertSee('pembelian_page=2', false);

        $pembelian = $response->viewData('pembelian');
        $this->assertSame(7, $pembelian->perPage());
        $this->assertSame(8, $pembelian->total());
        $this->assertCount(7, $pembelian->items());
    }
}
