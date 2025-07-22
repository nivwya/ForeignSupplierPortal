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

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreign('vendor_id')->references('vendor_id')->on('purchase_orders');
            $table->string('vendor_code')->unique();
            $table->string('vendor_name');
            $table->date('created_at');
            $table->date('modified_at');
            $table->index('vendor_code');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
