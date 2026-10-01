@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history"></i> Riwayat Transaksi</h1>
    <a href="/kasir" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-cash-register"></i> Kembali ke Kasir
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form method="GET" action="{{ route('kasir.riwayat') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('kasir.riwayat') }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
            <div class="col-md-3 text-end">
                <div class="small text-muted">Total Pendapatan (Filtered)</div>
                <div class="h5 mb-0 fw-bold text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Item</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr>
                        <td>{{ $transactions->firstItem() + $index }}</td>
                        <td><span class="badge bg-primary">{{ $trx->transaction_number }}</span></td>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $trx->user->name ?? 'Admin' }}</td>
                        <td>{{ $trx->payment_method }}</td>
                        <td>{{ $trx->details->sum('qty') }} item</td>
                        <td class="text-end fw-bold">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('kasir.struk', $trx->id) }}" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-receipt"></i> Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $transactions->withQueryString()->links() }}</div>
    </div>
</div>
@endsection