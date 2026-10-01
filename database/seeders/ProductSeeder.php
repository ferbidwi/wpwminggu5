<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            ['code' => 'BRG001', 'barcode' => '899123456001', 'name' => 'Indomie Goreng', 'price' => 3500, 'stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BRG002', 'barcode' => '899123456002', 'name' => 'Aqua 600ml', 'price' => 4000, 'stock' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BRG003', 'barcode' => '899123456003', 'name' => 'Teh Botol Sosro', 'price' => 5000, 'stock' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BRG004', 'barcode' => '899123456004', 'name' => 'Beras 5 Kg', 'price' => 75000, 'stock' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BRG005', 'barcode' => '899123456005', 'name' => 'Minyak Goreng 1 Liter', 'price' => 18000, 'stock' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}