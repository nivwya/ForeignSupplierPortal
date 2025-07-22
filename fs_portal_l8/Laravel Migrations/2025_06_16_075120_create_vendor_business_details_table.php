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

        Schema::create('vendor_business_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->string('supplier_type', 50);
            $table->string('supplier_status', 50);
            $table->string('supplier_classification', 50);
            $table->string('supplier_category', 50);
            $table->string('payment_terms', 100);
            $table->string('currency', 10);
            $table->string('tax_number', 50);
            $table->string('vat_number');
            $table->string('registration_number', 50);
            $table->string('license_number', 50);
            $table->date('license_expiry');
            $table->string('website', 100)->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('vendor_business_details');
    }
};
