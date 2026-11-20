<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('products')) Schema::create('products', function (Blueprint $table) { $table->id(); $table->string('name',100); $table->decimal('price',10,2); $table->unsignedInteger('stock_quantity')->default(0); $table->string('image')->nullable(); $table->boolean('is_archived')->default(false); $table->dateTime('created_at')->nullable(); });
        if (!Schema::hasTable('customers')) Schema::create('customers', function (Blueprint $table) { $table->id(); $table->string('full_name',100); $table->string('email',100); $table->string('phone',20)->nullable(); $table->dateTime('created_at')->nullable(); });
        if (!Schema::hasTable('users')) Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('username',50)->unique(); $table->string('full_name',100); $table->string('password'); $table->string('avatar')->nullable(); $table->dateTime('created_at')->nullable(); });
        if (!Schema::hasTable('sales')) Schema::create('sales', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->constrained('products')->restrictOnDelete()->restrictOnUpdate(); $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete()->restrictOnUpdate(); $table->foreignId('sold_by')->constrained('users')->restrictOnDelete()->restrictOnUpdate(); $table->unsignedInteger('quantity'); $table->decimal('total_price',10,2); $table->string('product_name',100); $table->string('customer_name',100); $table->string('staff_name',100); $table->string('request_key',64)->unique(); $table->dateTime('created_at')->nullable(); });
    }
    public function down(): void { Schema::dropIfExists('sales'); Schema::dropIfExists('users'); Schema::dropIfExists('customers'); Schema::dropIfExists('products'); }
};
