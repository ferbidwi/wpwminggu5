<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;

class BukuController extends Controller
{
    // 1. Menampilkan Daftar Buku (Read)
    public function index()
    {
        $bukus = Buku::all(); // Mengambil semua data pakai Eloquent
        return view('buku.index', compact('bukus'));
    }

    // 2. Menampilkan Form Tambah Buku
    public function create()
    {
        return view('buku.create');
    }

    // 3. Menyimpan Data Buku Baru (Create) + Validasi (Acara 20)
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|min:3|max:100',
            'penulis' => 'required|min:3',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        Buku::create($request->all());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    // 4. Menampilkan Form Edit Buku
    public function edit(Buku $buku)
    {
        return view('buku.edit', compact('buku'));
    }

    // 5. Menyimpan Perubahan Data Buku (Update)
    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'judul' => 'required|min:3|max:100',
            'penulis' => 'required|min:3',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        $buku->update($request->all());

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diupdate!');
    }

    // 6. Menghapus Data Buku (Delete)
    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus!');
    }
}