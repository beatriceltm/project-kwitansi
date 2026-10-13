<?php

use App\Models\Team;
use App\Models\Transaksi;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

new class extends Component
{
    #[Locked]
    public int $teamId;

    #[Locked]
    public string $teamSlug;

    public string $cari = '';

    public function mount(string $current_team): void
    {
        // Cari tim berdasarkan parameter URL
        $team = Team::where('slug', $current_team)
            ->firstOrFail();

        // Pastikan pengguna merupakan anggota tim
        abort_unless(
            auth()->user()->teams()
                ->whereKey($team->id)
                ->exists(),
            403
        );

        $this->teamId = $team->id;
        $this->teamSlug = $team->slug;
    }

    #[Computed]
    public function transaksis()
    {
        // Periksa kembali keanggotaan tim
        abort_unless(
            auth()->user()->teams()
                ->whereKey($this->teamId)
                ->exists(),
            403
        );

        return Transaksi::query()
            ->where('team_id', $this->teamId)
            ->when($this->cari, function ($query) {
                $query->where(function ($q) {
                    $q->where(
                        'nama_pelanggan',
                        'like',
                        '%' . $this->cari . '%'
                    )
                    ->orWhere(
                        'nomor_kuitansi',
                        'like',
                        '%' . $this->cari . '%'
                    );
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(100)
            ->get();
    }
};

?>

<div class="mx-auto max-w-6xl p-6">

    <!-- Judul halaman -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold">
                Daftar Transaksi
            </h1>

            <p class="text-gray-500">
                Kelola pembayaran dan kuitansi pelanggan.
            </p>
        </div>

        <a
            href="{{ route('transaksi.create', [
                'current_team' => $teamSlug
            ]) }}"
            class="rounded-lg bg-blue-600 px-5 py-3 text-white"
        >
            + Tambah Transaksi
        </a>
    </div>

    <!-- Pencarian -->
    <div class="mb-5">
        <input
            type="text"
            wire:model.live.debounce.300ms="cari"
            placeholder="Cari nama pelanggan atau nomor kuitansi..."
            class="w-full rounded-lg border p-3"
        >
    </div>

    <!-- Tabel transaksi -->
    <div class="overflow-x-auto rounded-lg border">

        <table class="w-full text-left">

            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="p-3">No.</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">No. Kuitansi</th>
                    <th class="p-3">Pelanggan</th>
                    <th class="p-3">Nominal</th>
                    <th class="p-3">Metode</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($this->transaksis as $transaksi)

                    <tr wire:key="transaksi-{{ $transaksi->id }}"
                        class="border-t">

                        <td class="p-3">
                            {{ $loop->iteration }}
                        </td>

                        <td class="p-3">
                            {{ $transaksi->tanggal->format('d/m/Y') }}
                        </td>

                        <td class="p-3">
                            {{ $transaksi->nomor_kuitansi ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $transaksi->nama_pelanggan }}
                        </td>

                        <td class="p-3">
                            Rp{{ number_format(
                                $transaksi->nominal,
                                0, ',', '.'
                            ) }}
                        </td>

                        <td class="p-3">
                            {{ ucfirst(
                                $transaksi->metode_pembayaran
                            ) }}
                        </td>

                        <td class="p-3">
                            @if(
        $transaksi->status === 'lunas'
        && $transaksi->nomor_kuitansi
    )
        <a
            href="{{ route('transaksi.kuitansi', [
                'current_team' => $teamSlug,
                'transaksi' => $transaksi->id
            ]) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="rounded bg-blue-600 px-3 py-2 text-white"
        >
            Cetak Kuitansi
        </a>
    @else
        <span class="text-gray-400">
            Belum tersedia
        </span>
    @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8"
                            class="p-6 text-center text-gray-500">
                            Belum ada transaksi.
                        </td>
                    </tr>

                @endforelse
            </tbody>

        </table>
    </div>

    <p class="mt-3 text-sm text-gray-500">
        Menampilkan maksimal 100 transaksi terbaru.
    </p>

</div>
