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

        Schema::create('vendor_address', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->enum('address_type', [""]);
            $table->string('address_line1');
            $table->string('address_line2');
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('postal_code');
            $table->string('po_box');
            $table->string('country');
            $table->string('country_code');
            $table->date('created_at');
            $table->date('updated_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_address');
    }
};
