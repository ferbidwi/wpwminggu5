<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q');
        if (empty($keyword)) return response()->json([]);

        $products = Product::where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('code', 'LIKE', "%{$keyword}%")
            ->orWhere('barcode', 'LIKE', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'grand_total' => 'required|numeric',
            'paid_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            // CEK STOK DULU
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if (!$product || $product->stock < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$item['name']} tidak cukup. Tersedia: " . ($product->stock ?? 0)
                    ], 400);
                }
            }

            // Generate Nomor Transaksi Sequential
            $today = date('Ymd');
            $lastTrx = Transaction::whereDate('created_at', today())->count();
            $trxNumber = 'TRX-' . $today . '-' . str_pad($lastTrx + 1, 3, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $trxNumber,
                'user_id' => Auth::id(),
                'subtotal' => $request->subtotal,
                'discount' => $request->discount_percent + $request->discount_amount,
                'tax' => $request->tax,
                'other_fee' => $request->other_fee,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($request->items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'product_code' => $item['code'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                Product::find($item['id'])->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cetakStruk($id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        return view('kasir.struk', compact('transaction'));
    }

    // HALAMAN RIWAYAT TRANSAKSI
    public function riwayat(Request $request)
    {
        $query = Transaction::with('details')->orderBy('created_at', 'desc');

        // Filter by tanggal
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $transactions = $query->paginate(15);
        $totalPendapatan = $query->sum('grand_total');

        return view('kasir.riwayat', compact('transactions', 'totalPendapatan'));
    }

    // DETAIL TRANSAKSI (dari riwayat)
    public function detail($id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        return view('kasir.struk', compact('transaction'));
    }
}