<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('orders', function (Blueprint $table) {
            // $table->id();
            // $table->string('order_code', 20)->unique();
            // $table->unsignedBigInteger('customer_id');
            // $table->unsignedBigInteger('branch_id');
            // $table->unsignedBigInteger('salesperson_id');
            // $table->timestamp('order_date')->useCurrent();
            // $table->enum('status', ['draft', 'confirmed', 'shipped', 'completed', 'cancelled'])->default('draft');
            // $table->decimal('amount', 15, 2)->default(0);
            // $table->decimal('discount_amount', 15, 2)->default(0);
            // $table->decimal('vat_amount', 15, 2)->default(0);
            // $table->decimal('shipping_fee', 15, 2)->default(0);
            // $table->integer('point')->default(0);
            // $table->decimal('total_amount', 15, 2);
            // $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'ewallet']);
            // $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            // $table->decimal('paid_amount', 15, 2)->default(0);
            // $table->text('delivery_address')->nullable();
            // $table->timestamp('delivery_date')->nullable();
            // $table->text('delivery_note')->nullable();
            // $table->text('note')->nullable();
            // $table->timestamps();

            // // Foreign keys
            // $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            // $table->foreign('salesperson_id')->references('id')->on('users')->cascadeOnDelete();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
