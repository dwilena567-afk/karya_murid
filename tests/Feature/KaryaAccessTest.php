<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Karya;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KaryaAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_homepage_shows_latest_approved_works(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $murid = User::factory()->create(['role' => 'murid']);
        $approvedKarya = Karya::create([
            'user_id' => $murid->id,
            'judul' => 'Karya Terbaru Disetujui',
            'deskripsi' => 'Karya yang tampil pada beranda.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        Karya::create([
            'user_id' => $murid->id,
            'judul' => 'Karya Belum Disetujui',
            'deskripsi' => 'Karya ini tidak boleh tampil pada beranda.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'pending',
        ]);

        $this->get(route('beranda.index'))
            ->assertOk()
            ->assertSee('Karya Terbaru')
            ->assertSee($approvedKarya->judul)
            ->assertSee('Pertanyaan Umum')
            ->assertSee('mailto:mail.smkpgri3malang@gmail.com', false)
            ->assertSee('https://wa.me/6282133000370', false)
            ->assertDontSee('Karya Belum Disetujui');
    }

    public function test_catalog_detail_has_controls_to_switch_between_karya_images(): void
    {
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $murid = User::factory()->create(['role' => 'murid']);
        $karya = Karya::create([
            'user_id' => $murid->id,
            'judul' => 'Karya Dengan Galeri',
            'deskripsi' => 'Karya dengan beberapa gambar.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/utama.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        $karya->gambarTambahan()->createMany([
            ['gambar' => 'karya_images/kedua.jpg'],
            ['gambar' => 'karya_images/ketiga.jpg'],
        ]);

        $this->get(route('katalog.show', $karya))
            ->assertOk()
            ->assertSee('data-gallery-control="previous"', false)
            ->assertSee('data-gallery-control="next"', false)
            ->assertSee('aria-label="Tampilkan gambar 3"', false)
            ->assertSee('Gambar berikutnya');
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
        Storage::disk('public')->put('karya_images/karya-test-tambahan.jpg', 'test image');

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
        $karya->gambarTambahan()->create(['gambar' => 'karya_images/karya-test-tambahan.jpg']);

        $this->actingAs($student)
            ->delete(route('karyamu.destroy', $karya))
            ->assertRedirect(route('karyamu.index'))
            ->assertSessionHas('success');

        Storage::disk('public')->assertMissing('karya_images/karya-test.jpg');
        Storage::disk('public')->assertMissing('karya_images/karya-test-tambahan.jpg');
    }

    public function test_student_can_delete_an_additional_image_individually(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('karya_images/utama.jpg', 'main image');
        Storage::disk('public')->put('karya_images/tambahan.jpg', 'additional image');

        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/utama.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        $gambarTambahan = $karya->gambarTambahan()->create(['gambar' => 'karya_images/tambahan.jpg']);

        $this->actingAs($student)
            ->delete(route('karyamu.gambar.destroy', [$karya->id, $gambarTambahan->id]))
            ->assertRedirect(route('karyamu.edit', $karya))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('karya_gambars', ['id' => $gambarTambahan->id]);
        $this->assertDatabaseHas('karyas', [
            'id' => $karya->id,
            'gambar' => 'karya_images/utama.jpg',
            'status_verifikasi' => 'pending',
        ]);
        Storage::disk('public')->assertMissing('karya_images/tambahan.jpg');
        Storage::disk('public')->assertExists('karya_images/utama.jpg');
    }

    public function test_deleting_the_main_image_promotes_an_additional_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('karya_images/utama.jpg', 'main image');
        Storage::disk('public')->put('karya_images/tambahan.jpg', 'additional image');

        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/utama.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        $karya->gambarTambahan()->create(['gambar' => 'karya_images/tambahan.jpg']);

        $this->actingAs($student)
            ->delete(route('karyamu.gambar.destroy', [$karya->id, 'utama']))
            ->assertRedirect(route('karyamu.edit', $karya))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('karyas', [
            'id' => $karya->id,
            'gambar' => 'karya_images/tambahan.jpg',
            'status_verifikasi' => 'pending',
        ]);
        $this->assertDatabaseCount('karya_gambars', 0);
        Storage::disk('public')->assertMissing('karya_images/utama.jpg');
        Storage::disk('public')->assertExists('karya_images/tambahan.jpg');
    }

    public function test_student_cannot_delete_another_students_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('karya_images/tambahan.jpg', 'additional image');

        $owner = User::factory()->create(['role' => 'murid']);
        $otherStudent = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $owner->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'pending',
        ]);
        $gambarTambahan = $karya->gambarTambahan()->create(['gambar' => 'karya_images/tambahan.jpg']);

        $this->actingAs($otherStudent)
            ->delete(route('karyamu.gambar.destroy', [$karya->id, $gambarTambahan->id]))
            ->assertNotFound();

        $this->assertDatabaseHas('karya_gambars', ['id' => $gambarTambahan->id]);
        Storage::disk('public')->assertExists('karya_images/tambahan.jpg');
    }

    public function test_student_can_upload_up_to_five_images_for_a_new_karya(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $gambar = collect(range(1, 5))
            ->map(fn ($number) => UploadedFile::fake()->create("gambar-{$number}.jpg", 100, 'image/jpeg'))
            ->all();

        $this->actingAs($student)
            ->post(route('karyamu.store'), [
                'judul' => 'Karya Dengan Banyak Gambar',
                'deskripsi' => 'Deskripsi karya dengan lima gambar.',
                'kategori_id' => $kategori->id,
                'harga' => 10000,
                'stok' => 1,
                'gambar' => $gambar,
            ])
            ->assertRedirect(route('karyamu.index'))
            ->assertSessionHas('success');

        $karya = Karya::where('judul', 'Karya Dengan Banyak Gambar')->firstOrFail();

        $this->assertDatabaseCount('karya_gambars', 4);
        $this->assertCount(5, $karya->semua_gambar);
        foreach ($karya->semua_gambar as $gambarPath) {
            Storage::disk('public')->assertExists($gambarPath);
        }
    }

    public function test_student_can_add_images_while_editing_a_karya(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('karya_images/utama.jpg', 'main image');
        Storage::disk('public')->put('karya_images/tambahan.jpg', 'additional image');

        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Sebelum Diedit',
            'deskripsi' => 'Deskripsi sebelum diedit.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/utama.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        $karya->gambarTambahan()->create(['gambar' => 'karya_images/tambahan.jpg']);
        $gambarBaru = [
            UploadedFile::fake()->create('gambar-baru-1.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('gambar-baru-2.jpg', 100, 'image/jpeg'),
        ];

        $this->actingAs($student)
            ->put(route('karyamu.update', $karya), [
                'judul' => 'Karya Setelah Diedit',
                'deskripsi' => 'Deskripsi setelah diedit.',
                'kategori_id' => $kategori->id,
                'harga' => 10000,
                'stok' => 1,
                'gambar_tambahan' => $gambarBaru,
            ])
            ->assertRedirect(route('karyamu.index'))
            ->assertSessionHas('success');

        $karya->refresh()->load('gambarTambahan');

        $this->assertCount(4, $karya->semua_gambar);
        $this->assertSame('pending', $karya->status_verifikasi);
        foreach ($karya->semua_gambar as $gambarPath) {
            Storage::disk('public')->assertExists($gambarPath);
        }
    }

    public function test_editing_karya_cannot_add_images_beyond_the_five_image_limit(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $karya = Karya::create([
            'user_id' => $student->id,
            'judul' => 'Karya Tes',
            'deskripsi' => 'Deskripsi karya tes.',
            'kategori_id' => $kategori->id,
            'gambar' => 'karya_images/utama.jpg',
            'harga' => 10000,
            'stok' => 1,
            'status_verifikasi' => 'approved',
        ]);
        foreach (range(1, 4) as $number) {
            $karya->gambarTambahan()->create(['gambar' => "karya_images/tambahan-{$number}.jpg"]);
        }
        $gambarTambahan = UploadedFile::fake()->create('gambar-tambahan.jpg', 100, 'image/jpeg');

        $this->actingAs($student)
            ->from(route('karyamu.edit', $karya))
            ->put(route('karyamu.update', $karya), [
                'judul' => 'Karya Tes',
                'deskripsi' => 'Deskripsi karya tes.',
                'kategori_id' => $kategori->id,
                'harga' => 10000,
                'stok' => 1,
                'gambar_tambahan' => [$gambarTambahan],
            ])
            ->assertRedirect(route('karyamu.edit', $karya))
            ->assertSessionHasErrors('gambar_tambahan');

        $this->assertDatabaseHas('karyas', [
            'id' => $karya->id,
            'status_verifikasi' => 'approved',
        ]);
        $this->assertDatabaseCount('karya_gambars', 4);
        $this->assertSame([], Storage::disk('public')->allFiles('karya_images'));
    }

    public function test_student_cannot_upload_more_than_five_images_for_a_new_karya(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'murid']);
        $kategori = Kategori::create(['nama' => 'Kategori Tes']);
        $gambar = collect(range(1, 6))
            ->map(fn ($number) => UploadedFile::fake()->create("gambar-{$number}.jpg", 100, 'image/jpeg'))
            ->all();

        $this->actingAs($student)
            ->from(route('karyamu.create'))
            ->post(route('karyamu.store'), [
                'judul' => 'Karya Terlalu Banyak Gambar',
                'deskripsi' => 'Deskripsi karya.',
                'kategori_id' => $kategori->id,
                'harga' => 10000,
                'stok' => 1,
                'gambar' => $gambar,
            ])
            ->assertRedirect(route('karyamu.create'))
            ->assertSessionHasErrors('gambar');

        $this->assertDatabaseMissing('karyas', ['judul' => 'Karya Terlalu Banyak Gambar']);
        $this->assertDatabaseCount('karya_gambars', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('karya_images'));
    }
}
