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

        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('delivery_id');
            $table->bigInteger('line_item_num');
            $table->string('item_description');
            $table->decimal('quantity');
            $table->string('uom', 10);
            $table->date('expected_delv_date');
            $table->decimal('quantity_supplied');
            $table->date('supply_date');
            $table->decimal('qty_received_by_amg');
            $table->date('amg_received_date');
            $table->bigInteger('item_id');
            $table->foreign('item_id')->references('item_id')->on('purchase_order_items');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
