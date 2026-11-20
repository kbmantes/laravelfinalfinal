<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('username', 'admin')->exists()) {
            User::create(['username'=>'admin','full_name'=>'Store Administrator','password'=>Hash::make('Admin123!'),'created_at'=>now()]);
        }

        if (Product::count() === 0) {
            Product::insert([
                ['name'=>'Notebook', 'price'=>'85.00', 'stock_quantity'=>50, 'is_archived'=>false, 'created_at'=>now()],
                ['name'=>'Gel Pen Set', 'price'=>'125.50', 'stock_quantity'=>30, 'is_archived'=>false, 'created_at'=>now()],
                ['name'=>'Coffee Mug', 'price'=>'199.00', 'stock_quantity'=>20, 'is_archived'=>false, 'created_at'=>now()],
            ]);
        }

        if (Customer::count() === 0) {
            Customer::insert([
                ['full_name'=>'Walk-in Customer', 'email'=>'walkin@example.com', 'phone'=>null, 'created_at'=>now()],
                ['full_name'=>'Sample Customer', 'email'=>'customer@example.com', 'phone'=>'09170000000', 'created_at'=>now()],
            ]);
        }
    }
}
