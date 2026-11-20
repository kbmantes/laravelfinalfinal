<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = ['username', 'full_name', 'password', 'avatar', 'created_at'];
    protected $hidden = ['password'];

    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function sales() { return $this->hasMany(Sale::class, 'sold_by'); }
}
