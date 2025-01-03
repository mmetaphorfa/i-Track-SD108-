<?php

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id', 30)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->text('full_name');
            $table->text('email');
            $table->text('phone');
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('trans_charge', 10, 2)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('bill_code', 30)->nullable()->unique();
            $table->string('receipt_id', 30)->nullable()->unique();
            $table->text('reference')->nullable();
            $table->string('status', 15)->default('pending')->comment('pending/paid/failed');
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
