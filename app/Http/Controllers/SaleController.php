<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SaleController extends Controller
{
    public function index() { $records = Sale::orderByDesc('id')->paginate(15); return view('sales.index', compact('records')); }
    public function create() { return view('sales.form', ['products'=>Product::where('is_archived',false)->orderBy('name')->get(), 'customers'=>Customer::orderBy('full_name')->get(), 'requestKey'=>bin2hex(random_bytes(32))]); }
    public function store(Request $request, SaleService $sales)
    {
        $data = $request->validate(['product_id'=>['required','integer','min:1'],'customer_id'=>['nullable','integer','min:1'],'quantity'=>['required','integer','min:1','max:1000000'],'request_key'=>['required','regex:/^[a-f0-9]{64}$/']]);
        try { $sale=$sales->record((int)$data['product_id'], $data['customer_id'] ? (int)$data['customer_id'] : null, (int)$request->user()->id, (int)$data['quantity'], $data['request_key']); }
        catch (InvalidArgumentException $e) { return back()->withInput()->withErrors(['sale'=>$e->getMessage()]); }
        return redirect()->route('sales.show',$sale)->with('success','Sale recorded successfully. Inventory updated.');
    }
    public function show(Sale $sale) { return view('sales.show', compact('sale')); }
}
