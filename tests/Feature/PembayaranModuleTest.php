<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_unique_monthly_invoice(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $penghuni = $this->createPenghuni();
        $invoice = [
            'penghuni_id' => $penghuni->id,
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'jumlah' => 850000,
            'jatuh_tempo' => '2026-11-30',
            'keterangan' => 'Tagihan sewa',
        ];

        $this->actingAs($admin)
            ->post(route('admin.pembayaran.store'), $invoice)
            ->assertRedirect(route('admin.pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'penghuni_id' => $penghuni->id,
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'jumlah' => 850000,
            'status' => 'belum_bayar',
        ]);

        $pembayaran = Pembayaran::firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.pembayaran.index'))
            ->assertOk()
            ->assertSee('Manajemen Tagihan');

        $this->actingAs($admin)
            ->get(route('admin.pembayaran.edit', $pembayaran))
            ->assertOk()
            ->assertSee('Edit Tagihan Sewa Kos');

        $this->actingAs($admin)
            ->post(route('admin.pembayaran.store'), $invoice)
            ->assertSessionHasErrors('periode_tahun');

        $this->actingAs($admin)
            ->put(route('admin.pembayaran.update', $pembayaran), [
                ...$invoice,
                'jumlah' => 900000,
                'keterangan' => 'Tagihan diperbarui',
            ])
            ->assertRedirect(route('admin.pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'jumlah' => 900000,
            'keterangan' => 'Tagihan diperbarui',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.pembayaran.destroy', $pembayaran))
            ->assertRedirect(route('admin.pembayaran.index'));

        $this->assertDatabaseMissing('pembayarans', ['id' => $pembayaran->id]);
    }

    public function test_overdue_status_and_monthly_report_use_the_selected_billing_period(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 25)->startOfDay());

        $admin = User::factory()->create(['role' => 'admin']);
        $penghuni = $this->createPenghuni();
        $overdueInvoice = $this->createPembayaran($penghuni, [
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'jatuh_tempo' => '2026-11-24',
            'jumlah' => 850000,
        ]);
        $paidInvoice = $this->createPembayaran($penghuni, [
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'jatuh_tempo' => '2026-11-20',
            'tanggal_bayar' => '2026-11-21',
            'metode' => 'transfer',
            'status' => 'lunas',
            'jumlah' => 500000,
        ]);
        $this->createPembayaran($penghuni, [
            'periode_bulan' => 12,
            'periode_tahun' => 2026,
            'jatuh_tempo' => '2026-12-30',
            'tanggal_bayar' => '2026-12-01',
            'metode' => 'tunai',
            'status' => 'lunas',
            'jumlah' => 700000,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.pembayaran.laporan', ['periode_bulan' => 11, 'periode_tahun' => 2026]));

        $response->assertOk()
            ->assertViewHas('totalPemasukan', 500000)
            ->assertViewHas('totalTunggakan', 850000)
            ->assertViewHas('lunasList', fn ($list): bool => $list->contains('id', $paidInvoice->id))
            ->assertViewHas('menunggakList', fn ($list): bool => $list->contains('id', $overdueInvoice->id));

        $this->assertDatabaseHas('pembayarans', [
            'id' => $overdueInvoice->id,
            'status' => 'terlambat',
        ]);
    }

    public function test_tenant_can_submit_payment_details_but_cannot_upload_for_another_tenant(): void
    {
        Storage::fake('local');

        $owner = $this->createPenghuni();
        $otherTenant = $this->createPenghuni();
        $pembayaran = $this->createPembayaran($owner);
        $payload = [
            'metode' => 'transfer',
            'tanggal_bayar' => '2026-11-25',
            'bukti_bayar' => UploadedFile::fake()->image('bukti.png'),
        ];

        $this->actingAs($otherTenant->user)
            ->post(route('penghuni.pembayaran.upload', $pembayaran), $payload)
            ->assertNotFound();

        $this->actingAs($owner->user)
            ->post(route('penghuni.pembayaran.upload', $pembayaran), [
                'metode' => 'transfer',
                'tanggal_bayar' => '2026-11-25',
            ])
            ->assertSessionHasErrors('bukti_bayar');

        $this->actingAs($owner->user)
            ->post(route('penghuni.pembayaran.upload', $pembayaran), $payload)
            ->assertSessionHas('success');

        $pembayaran->refresh();
        $this->assertSame('transfer', $pembayaran->metode);
        $this->assertSame('2026-11-25', $pembayaran->tanggal_bayar->toDateString());
        $this->assertSame('belum_bayar', $pembayaran->status);
        Storage::disk('local')->assertExists($pembayaran->bukti_bayar);

        $this->actingAs($owner->user)
            ->get(route('penghuni.pembayaran.index'))
            ->assertOk()
            ->assertSee('Tagihan & Riwayat Pembayaran Saya', false);

        $this->actingAs($owner->user)
            ->get(route('penghuni.pembayaran.bukti', $pembayaran))
            ->assertOk();

        $this->actingAs($otherTenant->user)
            ->get(route('penghuni.pembayaran.bukti', $pembayaran))
            ->assertNotFound();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('admin.pembayaran.konfirmasi', $pembayaran), ['status' => 'lunas'])
            ->assertSessionHasErrors(['metode', 'tanggal_bayar']);

        $this->actingAs($admin)
            ->put(route('admin.pembayaran.konfirmasi', $pembayaran), [
                'status' => 'lunas',
                'metode' => 'transfer',
                'tanggal_bayar' => '2026-11-25',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status' => 'lunas',
            'metode' => 'transfer',
        ]);

        $this->assertSame('2026-11-25', $pembayaran->fresh()->tanggal_bayar->toDateString());
    }

    private function createPenghuni(): Penghuni
    {
        $user = User::factory()->create(['role' => 'penghuni']);
        $kamar = Kamar::create([
            'nomor_kamar' => 'K-'.fake()->unique()->numerify('###'),
            'tipe' => 'Reguler',
            'harga_per_bulan' => 500000,
            'kapasitas' => 1,
            'status' => 'terisi',
        ]);

        return Penghuni::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => fake()->unique()->numerify('##############'),
            'alamat_asal' => 'Alamat uji',
            'pekerjaan' => 'Mahasiswa',
            'nama_kontak_darurat' => 'Kontak Uji',
            'no_kontak_darurat' => '081234567890',
            'tanggal_masuk' => '2026-01-01',
            'status' => 'aktif',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createPembayaran(Penghuni $penghuni, array $attributes = []): Pembayaran
    {
        return Pembayaran::create([
            'penghuni_id' => $penghuni->id,
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'jumlah' => 500000,
            'jatuh_tempo' => '2026-11-30',
            'status' => 'belum_bayar',
            ...$attributes,
        ]);
    }
}
