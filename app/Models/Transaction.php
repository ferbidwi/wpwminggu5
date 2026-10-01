<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_number', 'user_id', 'subtotal', 'discount', 'tax', 'other_fee', 'grand_total', 'paid_amount', 'change_amount', 'payment_method'];

    // TAMBAHKAN RELASI INI
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}