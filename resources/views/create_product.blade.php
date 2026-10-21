<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <!-- Panggil CSS Bootstrap biar rapi (opsional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Tambah Produk Baru</h2>

    <!-- Jangan lupa enctype="multipart/form-data" -->
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label>Nama Produk:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Harga:</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Stok:</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        
        <!-- INI YANG PENTING UNTUK UPLOAD GAMBAR -->
        <div class="mb-3">
            <label>Gambar Produk:</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Produk</button>
        <a href="{{ route('admin') }}" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>