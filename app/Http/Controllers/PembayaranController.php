<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Penghuni;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembayaranController extends Controller
{
    public function indexAdmin(Request $request): View
    {
        $this->markOverduePayments();

        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['belum_bayar', 'lunas', 'terlambat'])],
            'periode_bulan' => ['nullable', 'integer', 'between:1,12'],
            'periode_tahun' => ['nullable', 'integer', 'min:2020', 'max:'.(now()->year + 5)],
        ]);

        $query = Pembayaran::with(['penghuni.user', 'penghuni.kamar']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['periode_bulan'])) {
            $query->where('periode_bulan', $filters['periode_bulan']);
        }

        if (isset($filters['periode_tahun'])) {
            $query->where('periode_tahun', $filters['periode_tahun']);
        }

        $pembayarans = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans', 'filters'));
    }

    public function create(): View
    {
        $penghunis = Penghuni::with(['user', 'kamar'])
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get();

        return view('admin.pembayaran.create', compact('penghunis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateInvoice($request);
        $validated['status'] = Carbon::parse($validated['jatuh_tempo'])->isBefore(today())
            ? 'terlambat'
            : 'belum_bayar';

        Pembayaran::create($validated);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Tagihan bulanan berhasil dibuat.');
    }

    public function edit(Pembayaran $pembayaran): View
    {
        $penghunis = Penghuni::with(['user', 'kamar'])->orderBy('id')->get();

        return view('admin.pembayaran.create', compact('penghunis', 'pembayaran'));
    }

    public function update(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $this->validateInvoice($request, $pembayaran);

        if ($pembayaran->status !== 'lunas') {
            $validated['status'] = Carbon::parse($validated['jatuh_tempo'])->isBefore(today())
                ? 'terlambat'
                : 'belum_bayar';
        }

        $pembayaran->update($validated);

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        $buktiBayar = $pembayaran->bukti_bayar;
        $pembayaran->delete();

        if ($buktiBayar !== null) {
            Storage::disk('local')->delete($buktiBayar);
            Storage::disk('public')->delete($buktiBayar);
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    public function konfirmasi(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['belum_bayar', 'lunas'])],
            'metode' => ['nullable', 'required_if:status,lunas', Rule::in(['tunai', 'transfer'])],
            'tanggal_bayar' => ['nullable', 'required_if:status,lunas', 'date', 'before_or_equal:today'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $isPaid = $validated['status'] === 'lunas';
        $updates = [
            'status' => $isPaid
                ? 'lunas'
                : (Carbon::parse($pembayaran->jatuh_tempo)->isBefore(today()) ? 'terlambat' : 'belum_bayar'),
        ];

        if ($isPaid) {
            $updates['metode'] = $validated['metode'];
            $updates['tanggal_bayar'] = $validated['tanggal_bayar'];
        } elseif ($pembayaran->status === 'lunas') {
            $updates['metode'] = null;
            $updates['tanggal_bayar'] = null;
        }

        if (array_key_exists('keterangan', $validated)) {
            $updates['keterangan'] = $validated['keterangan'];
        }

        $pembayaran->update($updates);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function downloadBukti(Pembayaran $pembayaran): StreamedResponse
    {
        $isAdmin = auth()->user()->role === 'admin';
        $isOwner = $pembayaran->penghuni()
            ->where('user_id', auth()->id())
            ->exists();

        abort_unless($isAdmin || $isOwner, 404);
        abort_if($pembayaran->bukti_bayar === null, 404);

        $disk = Storage::disk('local')->exists($pembayaran->bukti_bayar)
            ? 'local'
            : 'public';

        abort_unless(Storage::disk($disk)->exists($pembayaran->bukti_bayar), 404);

        return Storage::disk($disk)->download(
            $pembayaran->bukti_bayar,
            basename($pembayaran->bukti_bayar),
        );
    }

    public function laporan(Request $request): View
    {
        $this->markOverduePayments();

        $filters = $request->validate([
            'periode_bulan' => ['nullable', 'integer', 'between:1,12'],
            'periode_tahun' => ['nullable', 'integer', 'min:2020', 'max:'.(now()->year + 5)],
        ]);
        $periodeBulan = (int) ($filters['periode_bulan'] ?? now()->month);
        $periodeTahun = (int) ($filters['periode_tahun'] ?? now()->year);

        $tagihanPeriode = Pembayaran::query()
            ->where('periode_bulan', $periodeBulan)
            ->where('periode_tahun', $periodeTahun);

        $lunasList = (clone $tagihanPeriode)
            ->with(['penghuni.user', 'penghuni.kamar'])
            ->where('status', 'lunas')
            ->latest('tanggal_bayar')
            ->get();
        $totalPemasukan = (clone $tagihanPeriode)
            ->where('status', 'lunas')
            ->sum('jumlah');
        $menunggakList = (clone $tagihanPeriode)
            ->with(['penghuni.user', 'penghuni.kamar'])
            ->where('status', 'terlambat')
            ->orderBy('jatuh_tempo')
            ->get();
        $totalTunggakan = (clone $tagihanPeriode)
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->sum('jumlah');

        return view('admin.pembayaran.laporan', compact(
            'lunasList',
            'menunggakList',
            'totalPemasukan',
            'totalTunggakan',
            'periodeBulan',
            'periodeTahun',
        ));
    }

    public function indexPenghuni(): View
    {
        $this->markOverduePayments();

        $penghuni = auth()->user()->penghuni;
        $pembayarans = $penghuni
            ? Pembayaran::where('penghuni_id', $penghuni->id)->latest()->paginate(10)
            : Pembayaran::whereKey(0)->paginate(10);

        return view('penghuni.pembayaran.index', compact('pembayarans', 'penghuni'));
    }

    public function uploadBukti(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless(
            $pembayaran->penghuni()->where('user_id', auth()->id())->exists(),
            404,
        );
        abort_if($pembayaran->status === 'lunas', 403);

        $this->markOverduePayments();

        $validated = $request->validate([
            'metode' => ['required', Rule::in(['tunai', 'transfer'])],
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:today'],
            'bukti_bayar' => [
                'nullable',
                'required_if:metode,transfer',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $oldProof = $pembayaran->bukti_bayar;
        $path = $request->file('bukti_bayar')?->store('bukti_bayar', 'local');
        $updates = [
            'metode' => $validated['metode'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'keterangan' => 'Bukti pembayaran telah dikirim penghuni, menunggu verifikasi admin.',
        ];

        if ($path !== null) {
            $updates['bukti_bayar'] = $path;
        }

        $pembayaran->update($updates);

        if ($path !== null && $oldProof !== null && $oldProof !== $path) {
            Storage::disk('local')->delete($oldProof);
            Storage::disk('public')->delete($oldProof);
        }

        return back()->with('success', 'Data pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateInvoice(Request $request, ?Pembayaran $pembayaran = null): array
    {
        $uniquePeriod = Rule::unique('pembayarans', 'periode_tahun')
            ->where('penghuni_id', $request->input('penghuni_id'))
            ->where('periode_bulan', $request->input('periode_bulan'));

        if ($pembayaran !== null) {
            $uniquePeriod->ignore($pembayaran->id);
        }

        return $request->validate([
            'penghuni_id' => ['required', 'exists:penghunis,id'],
            'periode_bulan' => ['required', 'integer', 'between:1,12'],
            'periode_tahun' => ['required', 'integer', 'min:2020', 'max:'.(now()->year + 5), $uniquePeriod],
            'jumlah' => ['required', 'integer', 'min:1'],
            'jatuh_tempo' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function markOverduePayments(): void
    {
        Pembayaran::query()
            ->where('status', 'belum_bayar')
            ->whereDate('jatuh_tempo', '<', today())
            ->update(['status' => 'terlambat']);
    }
}
