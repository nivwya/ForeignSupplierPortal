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

        Schema::create('vendor_purchasing_org', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->string('purchasing_org');
            $table->string('order_currency');
            $table->decimal('min_order_value', 18, 2);
            $table->string('terms_of_payment');
            $table->string('incoterms');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_purchasing_org');
    }
};
