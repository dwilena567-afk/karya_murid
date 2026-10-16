<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Karya;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KaryaAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_buyer_cannot_see_or_access_karyamu_and_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);

        $this->actingAs($buyer)
            ->get(route('beranda.index'))
            ->assertOk()
            ->assertDontSee('Karyamu')
            ->assertDontSee('Dashboard');

        $this->get(route('karyamu.index'))
            ->assertRedirect(route('katalog.index'));

        $this->get(route('karyamu.create'))
            ->assertRedirect(route('katalog.index'));

        $this->get(route('dashboard.index'))
            ->assertRedirect(route('katalog.index'));
    }

    public function test_student_and_admin_can_see_and_access_karyamu_and_dashboard(): void
    {
        foreach (['murid', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)
                ->get(route('beranda.index'))
                ->assertOk()
                ->assertSee('Karyamu')
                ->assertSee('Dashboard');

            if ($role === 'murid') {
                $response->assertDontSee('Verifikasi Karya');
            }

            $this->get(route('karyamu.index'))->assertOk();
            $this->get(route('karyamu.create'))->assertOk();
            $this->get(route('dashboard.index'))->assertOk();
        }
    }

    public function test_admin_can_verify_pending_karya_from_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => User::factory()->create(['role' => 'murid'])->id,
            'judul' => 'Karya Menunggu Verifikasi',
            'deskripsi' => 'Deskripsi karya yang menunggu verifikasi.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Verifikasi Karya')
            ->assertSee('Karya Menunggu Verifikasi')
            ->assertSee(route('verifikasi.approve', $karya), false)
            ->assertSee(route('verifikasi.reject', $karya), false);

        $this->get(route('verifikasi.index'))
            ->assertRedirect(route('dashboard.index') . '#verifikasi');

        $this->patch(route('verifikasi.approve', $karya))
            ->assertRedirect(route('dashboard.index') . '#verifikasi')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('karyas', [
            'id' => $karya->id,
            'status_verifikasi' => 'approved',
        ]);
    }

    public function test_create_and_edit_forms_warn_when_price_is_zero(): void
    {
        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'pending',
        ]);

        $this->actingAs($student)
            ->get(route('karyamu.create'))
            ->assertOk()
            ->assertSee('data-confirm-price-zero', false);

        $this->get(route('karyamu.edit', $karya))
            ->assertOk()
            ->assertSee('data-confirm-price-zero', false);
    }

    public function test_deleting_karya_also_deletes_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('karya_images/karya-test.jpg', 'test image');

        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/karya-test.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'pending',
        ]);

        $this->actingAs($student)
            ->delete(route('karyamu.destroy', $karya))
            ->assertRedirect(route('karyamu.index'))
            ->assertSessionHas('success');

        Storage::disk('public')->assertMissing('karya_images/karya-test.jpg');
    }
}
