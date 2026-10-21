<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tambahkan 'image' di sini
    protected $fillable = [
        'name',   // sesuaikan dengan nama kolom di db kamu (bisa jadi product_name)
        'price', 
        'stock', 
        'image'
    ];
}