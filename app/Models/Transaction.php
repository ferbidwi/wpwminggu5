<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number', 
        'user_id', 
        'subtotal', 
        'discount', 
        'tax', 
        'other_fee', 
        'grand_total', 
        'paid_amount', 
        'change_amount', 
        'payment_method'
    ];

    // Relasi ke Detail Transaksi
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // TAMBAHKAN RELASI INI (Biar bisa manggil nama kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}