<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $archived = $request->boolean('archived'); $q = trim((string)$request->query('q', ''));
        $records = Product::where('is_archived', $archived)->when($q, fn($query) => $query->where('name', 'like', '%'.mb_substr($q, 0, 100).'%'))->orderByDesc('id')->paginate(8)->withQueryString();
        return view('products.index', compact('records', 'archived', 'q'));
    }
    public function create() { return view('products.form', ['product' => new Product]); }
    public function store(Request $request, ImageStorageService $images)
    {
        $data = $request->validate(['name' => ['required','string','max:100'], 'price' => ['required','numeric','gt:0','max:99999999.99','regex:/^\d{1,8}(\.\d{1,2})?$/'], 'stock_quantity' => ['required','integer','min:0','max:1000000'], 'image' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:2048']]);
        $data['price'] = number_format((float)$data['price'], 2, '.', '');
        try { if ($name = $images->save($request->file('image'))) $data['image'] = $name; } catch (InvalidArgumentException $e) { throw ValidationException::withMessages(['image' => $e->getMessage()]); }
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product added.');
    }
    public function edit(Product $product) { return view('products.form', compact('product')); }
    public function update(Request $request, Product $product, ImageStorageService $images)
    {
        $data = $request->validate(['name' => ['required','string','max:100'], 'price' => ['required','numeric','gt:0','max:99999999.99','regex:/^\d{1,8}(\.\d{1,2})?$/'], 'stock_quantity' => ['required','integer','min:0','max:1000000'], 'image' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:2048']]);
        $data['price'] = number_format((float)$data['price'], 2, '.', '');
        try { if ($name = $images->save($request->file('image'))) { $images->delete($product->image); $data['image'] = $name; } } catch (InvalidArgumentException $e) { throw ValidationException::withMessages(['image' => $e->getMessage()]); }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated.');
    }
    public function destroy(Product $product, ImageStorageService $images)
    {
        if (!$product->is_archived) { $product->update(['is_archived' => true]); $message = 'Product archived. Sales history is preserved.'; }
        else { $product->update(['is_archived' => false]); $message = 'Product restored.'; }
        return redirect()->route('products.index', ['archived' => (int)$product->is_archived])->with('success', $message);
    }
    public function archive(Product $product) { $product->update(['is_archived' => !$product->is_archived]); return back()->with('success', $product->is_archived ? 'Product archived.' : 'Product restored.'); }
}
