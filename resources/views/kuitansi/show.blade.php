<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Kuitansi {{ $transaksi->nomor_kuitansi }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            color: #1e293b;
        }

        .toolbar {
            max-width: 800px;
            margin: 25px auto;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .btn {
            padding: 12px 22px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            background: #2563eb;
            color: white;
        }

        .kuitansi {
            background: #ffffff;
            width: 800px;
            max-width: 95%;
            margin: 20px auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px #00000015;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 18px;
        }

        .header h2 {
            margin: 0;
            color: #1e40af;
            font-size: 20px;
        }

        .header p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .judul {
            text-align: center;
            margin: 30px 0;
        }

        .judul h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 3px;
        }

        .judul p {
            margin-top: 8px;
            color: #64748b;
        }

        .detail {
            width: 100%;
            border-collapse: collapse;
        }

        .detail td {
            padding: 12px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .detail td:first-child {
            width: 35%;
            color: #64748b;
        }

        .jumlah {
            background: #eff6ff;
            padding: 20px;
            margin-top: 25px;
            border-radius: 8px;
        }

        .jumlah h2 {
            margin: 8px 0;
            font-size: 28px;
            color: #1e40af;
        }

        .terbilang {
            font-style: italic;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 35px;
        }

        .status {
            padding: 9px 18px;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
        }

        .ttd {
            text-align: center;
            min-width: 190px;
        }

        .ruang-ttd {
            height: 80px;
        }

        .catatan {
            text-align: center;
            border-top: 1px solid #e2e8f0;
            margin-top: 35px;
            padding-top: 15px;
            color: #64748b;
            font-size: 12px;
        }

        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }

            body {
                background: white;
            }

            .toolbar {
                display: none;
            }

            .kuitansi {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .jumlah {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol cetak -->
    <div class="toolbar">
        <button
            class="btn"
            onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Dokumen kuitansi -->
    <div class="kuitansi">

        <div class="header">
            <div>
                <h2>SISTEM MANAJEMEN OPERASIONAL</h2>
                <p>Bukti Penerimaan Pembayaran</p>
            </div>

            <div>
                <strong>KUITANSI RESMI</strong>
            </div>
        </div>

        <div class="judul">
            <h1>KUITANSI</h1>
            <p>
                No.
                {{ $transaksi->nomor_kuitansi }}
            </p>
        </div>

        <table class="detail">

            <tr>
                <td>Tanggal Pembayaran</td>
                <td>
                    {{ $transaksi->tanggal->translatedFormat('d F Y') }}
                </td>
            </tr>

            <tr>
                <td>Telah Diterima Dari</td>
                <td>
                    <strong>
                        {{ $transaksi->nama_pelanggan }}
                    </strong>
                </td>
            </tr>

            <tr>
                <td>Untuk Pembayaran</td>
                <td>
                    {{ $transaksi->keterangan }}
                </td>
            </tr>

            <tr>
                <td>Metode Pembayaran</td>
                <td>
                    {{ $transaksi->metode_pembayaran === 'transfer'
                        ? 'Transfer Bank'
                        : 'Tunai' }}
                </td>
            </tr>

        </table>

        <div class="jumlah">
            <strong>JUMLAH PEMBAYARAN</strong>

            <h2>
                Rp{{ number_format(
                    $transaksi->nominal,
                    0,
                    ',',
                    '.'
                ) }}
            </h2>

            <div class="terbilang">
                Terbilang:
                {{ $terbilang }}
            </div>
        </div>

        <div class="footer">

            <div>
                <span class="status">
                    LUNAS
                </span>
            </div>

            <div class="ttd">
                <p>Petugas Penerima,</p>

                <div class="ruang-ttd"></div>

                <p>
                    ({{ $namaPetugas }})
                </p>
            </div>

        </div>

        <p class="catatan">
            Kuitansi ini diterbitkan berdasarkan
            transaksi pembayaran yang tercatat
            dalam sistem.
        </p>

    </div>

</body>
</html>
