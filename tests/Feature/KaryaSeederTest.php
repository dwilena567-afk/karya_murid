<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryaSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_karya_seeder_creates_sample_artworks_and_can_run_more_than_once(): void
    {
        $this->seed(\Database\Seeders\KaryaSeeder::class);

        $this->assertDatabaseCount('karyas', 10);
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

        $this->seed(\Database\Seeders\KaryaSeeder::class);

        $this->assertDatabaseCount('karyas', 10);
    }
}
