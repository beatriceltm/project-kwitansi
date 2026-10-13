<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KuitansiController extends Controller
{
    /**
     * Menampilkan kuitansi transaksi lunas.
     */
    public function show(
        Request $request,
        string $current_team,
        int $transaksi
    ): View {
        // 1. Pastikan pengguna sudah login
        $user = $request->user();

        abort_unless($user, 403);

        // 2. Cari tim berdasarkan slug URL
        $team = Team::where('slug', $current_team)
            ->firstOrFail();

        // 3. Pastikan pengguna anggota tim
        abort_unless(
            $user->belongsToTeam($team),
            403,
            'Anda tidak memiliki akses ke tim ini.'
        );

        // 4. Cari transaksi hanya dari tim tersebut
        $data = Transaksi::query()
            ->with('user')
            ->where('team_id', $team->id)
            ->findOrFail($transaksi);

        // 5. Kuitansi hanya untuk transaksi lunas
        abort_unless(
            $data->status === 'lunas',
            403,
            'Transaksi belum lunas.'
        );

        // 6. Pastikan nomor kuitansi tersedia
        abort_unless(
            filled($data->nomor_kuitansi),
            403,
            'Nomor kuitansi belum tersedia.'
        );

        // 7. Siapkan data untuk template
        $terbilang = ucfirst(
            $this->angkaKeKata((int) $data->nominal)
        ) . ' rupiah';

        $namaPetugas = $data->user?->name
            ?? 'Petugas';

        // 8. Tampilkan template Blade
        return view('kuitansi.show', [
            'transaksi' => $data,
            'terbilang' => $terbilang,
            'namaPetugas' => $namaPetugas,
        ]);
    }

    /**
     * Mengubah angka menjadi kata bahasa Indonesia.
     */
    private function angkaKeKata(int $angka): string
    {
        if ($angka === 0) {
            return 'nol';
        }

        $satuan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
        ];

        $tingkatan = [
            '',
            'ribu',
            'juta',
            'miliar',
            'triliun',
            'kuadriliun',
        ];

        $bagian = [];
        $tingkat = 0;

        while ($angka > 0) {
            $kelompok = $angka % 1000;

            if ($kelompok > 0) {
                if ($tingkat === 1 && $kelompok === 1) {
                    $teks = 'seribu';
                } else {
                    $teks = $this->ratusanKeKata(
                        $kelompok,
                        $satuan
                    );

                    if ($tingkat > 0) {
                        $teks .= ' ' . $tingkatan[$tingkat];
                    }
                }

                array_unshift($bagian, $teks);
            }

            $angka = intdiv($angka, 1000);
            $tingkat++;
        }

        return implode(' ', $bagian);
    }

    /**
     * Mengubah angka 1 sampai 999 menjadi kata.
     */
    private function ratusanKeKata(
        int $angka,
        array $satuan
    ): string {
        $kata = [];

        $ratus = intdiv($angka, 100);
        $sisa = $angka % 100;

        if ($ratus === 1) {
            $kata[] = 'seratus';
        } elseif ($ratus > 1) {
            $kata[] = $satuan[$ratus] . ' ratus';
        }

        if ($sisa >= 20) {
            $puluh = intdiv($sisa, 10);
            $unit = $sisa % 10;

            $kata[] = $satuan[$puluh] . ' puluh';

            if ($unit > 0) {
                $kata[] = $satuan[$unit];
            }
        } elseif ($sisa >= 12) {
            $kata[] = $satuan[$sisa - 10] . ' belas';
        } elseif ($sisa === 11) {
            $kata[] = 'sebelas';
        } elseif ($sisa === 10) {
            $kata[] = 'sepuluh';
        } elseif ($sisa > 0) {
            $kata[] = $satuan[$sisa];
        }

        return implode(' ', $kata);
    }
}
