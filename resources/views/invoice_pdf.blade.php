<!DOCTYPE html>
<html>
<head>
    <title>Invoice PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .total-table { width: 40%; float: right; border-collapse: collapse; }
        .total-table td { padding: 6px; border-bottom: 1px solid #eee; }
        .footer { clear: both; text-align: center; margin-top: 50px; font-size: 11px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h2>TOKO RETAIL MAKMUR</h2>
        <p>Jl. Contoh No. 123 • Jember<br>Telp. 0812-xxxx-xxxx</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>No. Transaksi:</strong> {{ $transaction->transaction_number }}</td>
            <td class="text-right"><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Kasir:</strong> {{ $transaction->user->name ?? 'Admin' }}</td>
            <td class="text-right"><strong>Metode:</strong> {{ $transaction->payment_method }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>{{ $item->qty }}</td>
                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon:</td>
            <td class="text-right">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak:</td>
            <td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Biaya Lain:</td>
            <td class="text-right">Rp {{ number_format($transaction->other_fee, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Grand Total:</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Bayar:</td>
            <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja. Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
    </div>

</body>
</html>