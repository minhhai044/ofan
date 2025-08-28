<?php

use App\Models\ProductCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductCategory::class)->constrained();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('code_misa')->unique();
            $table->string('slug')->unique();
            $table->decimal('price', 20, 0)->default(0);
            $table->decimal('price_sale', 20, 0)->default(0);
            $table->boolean('is_active')->default(1); // 0: Ngừng hoạt động, 1: Hoạt động
            $table->json('images')->nullable();
            $table->integer('maintenance_schedule')->default(0); // Thời gian bảo trì (tháng)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
