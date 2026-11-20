<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'productCount' => Product::where('is_archived', false)->count(), 'customerCount' => Customer::count(), 'staffCount' => User::count(),
            'saleCount' => Sale::count(), 'revenue' => Sale::sum('total_price'), 'recentSales' => Sale::latest('id')->limit(5)->get(),
            'lowStock' => Product::where('is_archived', false)->where('stock_quantity', '<=', 5)->orderBy('stock_quantity')->get(),
        ]);
    }
}
