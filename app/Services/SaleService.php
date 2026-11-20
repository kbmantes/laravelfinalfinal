<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class SaleService
{
    public function record(int $productId, ?int $customerId, int $staffId, int $quantity, string $requestKey): Sale
    {
        if ($quantity < 1 || $quantity > 1000000 || !preg_match('/^[a-f0-9]{64}$/D', $requestKey)) throw new InvalidArgumentException('Enter a valid quantity and submit the sale form again.');
        $existing = Sale::where('request_key', $requestKey)->first();
        if ($existing) {
            if ((int)$existing->sold_by !== $staffId) throw new InvalidArgumentException('This sale reference belongs to another account.');
            return $existing;
        }
        try {
            return DB::transaction(function () use ($productId, $customerId, $staffId, $quantity, $requestKey): Sale {
                $changed = Product::whereKey($productId)->where('is_archived', false)->where('stock_quantity', '>=', $quantity)->decrement('stock_quantity', $quantity);
                if ($changed !== 1) throw new InvalidArgumentException('Sale rejected: the product is unavailable or the quantity exceeds available stock.');
                $product = Product::find($productId);
                $staff = User::find($staffId);
                $customer = $customerId ? Customer::find($customerId) : null;
                if (!$staff || ($customerId && !$customer)) throw new InvalidArgumentException('The selected customer or staff account no longer exists.');
                $cents = (int) round((float)$product->price * 100); $totalCents = $cents * $quantity;
                if ($totalCents > 9999999999) throw new InvalidArgumentException('Sale total exceeds the database limit. Reduce the quantity.');
                return Sale::create([
                    'product_id' => $product->id, 'customer_id' => $customer?->id, 'sold_by' => $staff->id,
                    'quantity' => $quantity, 'total_price' => number_format($totalCents / 100, 2, '.', ''),
                    'product_name' => $product->name, 'customer_name' => $customer?->full_name ?? 'Walk-in customer',
                    'staff_name' => $staff->full_name, 'request_key' => $requestKey, 'created_at' => now(),
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = Sale::where('request_key', $requestKey)->where('sold_by', $staffId)->first();
            if ($existing) return $existing;
            throw $e;
        } catch (\Throwable $e) {
            if ($e instanceof InvalidArgumentException) throw $e;
            report($e); throw new RuntimeException('The sale could not be saved. Stock was not deducted. Please try again.');
        }
    }
}
