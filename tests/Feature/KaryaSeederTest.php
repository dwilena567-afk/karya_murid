<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KaryaSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_karya_seeder_creates_sample_artworks_and_can_run_more_than_once(): void
    {
        $this->seed(\Database\Seeders\KaryaSeeder::class);

        $this->assertDatabaseCount('karyas', 23);
        $this->assertSame(16, DB::table('karyas')->where('status_verifikasi', 'pending')->count());
        $this->assertDatabaseHas('users', [
            'email' => 'murid.demo@example.test',
            'role' => 'murid',
        ]);
        $this->assertDatabaseHas('karyas', [
            'judul' => 'Senja di Ujung Desa',
            'status_verifikasi' => 'approved',
        ]);
        $this->assertDatabaseHas('karyas', [
            'judul' => 'Sketsa Pasar Tradisional',
            'status_verifikasi' => 'pending',
        ]);
        $this->assertDatabaseHas('karyas', [
            'judul' => 'Kartu Ucapan Buatan Tangan',
            'status_verifikasi' => 'pending',
        ]);

        $this->seed(\Database\Seeders\KaryaSeeder::class);

        $this->assertDatabaseCount('karyas', 23);
        $this->assertSame(16, DB::table('karyas')->where('status_verifikasi', 'pending')->count());
    }
}
