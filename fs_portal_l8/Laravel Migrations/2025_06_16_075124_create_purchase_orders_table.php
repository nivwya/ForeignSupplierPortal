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
        Schema::disableForeignKeyConstraints();

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreign('order_id')->references('order_id')->on('deliveries');
            $table->string('order_number')->unique();
            $table->bigInteger('vendor_id');
            $table->date('order_date');
            $table->date('delivery_date');
            $table->string('company', 100);
            $table->string('department', 100);
            $table->decimal('order_value', 18, 2);
            $table->string('currency', 10);
            $table->string('payment_term', 20);
            $table->string('status', 50);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
