@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Buku</h1>
    <a href="{{ route('buku.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('buku.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}">
                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" class="form-control @error('penulis') is-invalid @enderror" value="{{ old('penulis') }}">
                @error('penulis') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit') }}">
                @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok') }}">
                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection