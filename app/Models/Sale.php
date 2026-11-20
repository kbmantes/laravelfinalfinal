<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    public $timestamps = false;
    protected $fillable = ['product_id', 'customer_id', 'sold_by', 'quantity', 'total_price', 'product_name', 'customer_name', 'staff_name', 'request_key', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function product() { return $this->belongsTo(Product::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function staff() { return $this->belongsTo(User::class, 'sold_by'); }
}
