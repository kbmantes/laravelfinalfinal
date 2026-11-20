<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = false;
    protected $fillable = ['name', 'price', 'stock_quantity', 'image', 'is_archived', 'created_at'];
    protected function casts(): array { return ['is_archived' => 'boolean', 'created_at' => 'datetime']; }
    public function sales() { return $this->hasMany(Sale::class); }
}
