<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $transaction->transaction_number }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; max-width: 300px; margin: 0 auto; padding: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        .btn { display: block; width: 100%; padding: 10px; color: white; border: none; cursor: pointer; margin-top: 10px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-print { background: #0d6efd; }
        .btn-pdf { background: #dc3545; }
        @media print {
            .btn { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center">
        <h3 style="margin: 0;">TOKO RETAIL MAKMUR</h3>
        <p style="margin: 0;">Jl. Contoh No. 123 • Jember</p>
        <p style="margin: 0;">Telp. 0812-xxxx-xxxx</p>
    </div>

    <div class="line"></div>

    <table>
        <tr><td>No. Transaksi</td><td class="text-right">{{ $transaction->transaction_number }}</td></tr>
        <tr><td>Tanggal</td><td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Kasir</td><td class="text-right">{{ $transaction->user->name ?? 'Admin' }}</td></tr>
        <tr><td>Metode</td><td class="text-right">{{ $transaction->payment_method }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach($transaction->details as $item)
        <tr>
            <td colspan="3">{{ $item->product_name }}</td>
        </tr>
        <tr>
            <td>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Subtotal</td><td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="text-right">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td></tr>
        <tr><td>Biaya Lain</td><td class="text-right">Rp {{ number_format($transaction->other_fee, 0, ',', '.') }}</td></tr>
        <tr><td><strong>TOTAL</strong></td><td class="text-right"><strong>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong></td></tr>
        <tr><td>Bayar</td><td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Kembali</td><td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td></tr>
    </table>

    <div class="line"></div>

    <div class="text-center">
        <p>Terima Kasih Telah Berbelanja!</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>

    <!-- Tombol Download PDF -->
    <a href="{{ route('kasir.download-pdf', $transaction->id) }}" class="btn btn-pdf">
        📄 DOWNLOAD PDF
    </a>

    <!-- Tombol Cetak Biasa -->
    <button class="btn btn-print" onclick="window.print()">🖨️ CETAK STRUK</button>

</body>
</html>