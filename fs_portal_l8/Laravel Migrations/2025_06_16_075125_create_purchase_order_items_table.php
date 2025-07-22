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

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->foreign('order_id')->references('order_id')->on('purchase_orders');
            $table->string('product_code', 50);
            $table->bigInteger('line_item_no');
            $table->string('item_description');
            $table->bigInteger('quantity');
            $table->string('uom');
            $table->decimal('price');
            $table->decimal('value');
            $table->string('plant');
            $table->string('slocc');
            $table->string('status');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
