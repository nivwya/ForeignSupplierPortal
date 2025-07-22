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

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreign('delivery_id')->references('delivery_id')->on('delivery_items');
            $table->bigInteger('order_id');
            $table->date('delivery_date');
            $table->bigInteger('delivery_number')->unique();
            $table->string('company', 100);
            $table->string('department', 100);
            $table->decimal('order_value');
            $table->string('currency', 10);
            $table->string('status', 50);
            $table->string('grn_num');
            $table->date('grn_date');
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
        Schema::dropIfExists('deliveries');
    }
};
