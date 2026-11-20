<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index() { $records = Customer::orderByDesc('id')->paginate(10); return view('customers.index', compact('records')); }
    public function create() { return view('customers.form', ['customer' => new Customer]); }
    public function store(Request $request) { $data = $request->validate(['full_name'=>['required','string','max:100'],'email'=>['required','email','max:100'],'phone'=>['nullable','max:20','regex:/^[0-9+() .-]*$/']]); Customer::create($data); return redirect()->route('customers.index')->with('success','Customer added.'); }
    public function edit(Customer $customer) { return view('customers.form', compact('customer')); }
    public function update(Request $request, Customer $customer) { $data = $request->validate(['full_name'=>['required','string','max:100'],'email'=>['required','email','max:100'],'phone'=>['nullable','max:20','regex:/^[0-9+() .-]*$/']]); $customer->update($data); return redirect()->route('customers.index')->with('success','Customer updated.'); }
    public function destroy(Customer $customer) { if ($customer->sales()->exists()) return back()->with('error','This customer has recorded sales and cannot be deleted. You can still edit their details.'); $customer->delete(); return redirect()->route('customers.index')->with('success','Customer deleted.'); }
}
