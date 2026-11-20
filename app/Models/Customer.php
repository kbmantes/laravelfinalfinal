<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    public $timestamps = false;
    protected $fillable = ['full_name', 'email', 'phone', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function sales() { return $this->hasMany(Sale::class); }
}
