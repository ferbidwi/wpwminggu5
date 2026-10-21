<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format; // <-- TAMBAHAN PENTING

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('index_product', compact('products'));
    }

    public function create()
    {
        return view('create_product');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Inisialisasi ImageManager
            $manager = ImageManager::usingDriver(Driver::class);
            $image = $manager->decode($file)->scale(width: 500);
            
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // --- PERBAIKAN: Gunakan Format::JPEG dan cast ke string ---
            Storage::disk('public')->put(
                'products/' . $filename, 
                (string) $image->encodeUsingFormat(Format::JPEG)
            );
            // --------------------------------------------------------

            $data['image'] = $filename;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('show_product', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        return view('edit_product', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $file = $request->file('image');
            
            // Inisialisasi ImageManager
            $manager = ImageManager::usingDriver(Driver::class);
            $image = $manager->decode($file)->scale(width: 500);
            
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // --- PERBAIKAN: Gunakan Format::JPEG dan cast ke string ---
            Storage::disk('public')->put(
                'products/' . $filename, 
                (string) $image->encodeUsingFormat(Format::JPEG)
            );
            // --------------------------------------------------------

            $data['image'] = $filename;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
}