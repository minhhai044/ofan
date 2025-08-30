<?php

use App\Models\Branch;
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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('code_misa')->nullable(); // name + phone để auto-gen
            $table->boolean('is_active')->default(1); // 0: Ngừng, 1: Hoạt động
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->string('code_misa'); // name + phone để auto-gen
            $table->boolean('gender')->default(0); // 0: Nam, 1: Nữ
            $table->integer('role')->default(0);   // 0: Member, 1: Admin
            $table->tinyInteger('is_active')->default(1); // 0: Ngừng, 1: Hoạt động
            $table->string('fcm_token')->nullable();
            $table->string('bank_info')->nullable();
            $table->string('bank_qr')->nullable();
            $table->string('basic_salary')->default(0);
            $table->date('birthday')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('password_reset_tokens');
    }
};
