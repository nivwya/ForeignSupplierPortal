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

        Schema::create('vendor_company_codes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->string('company_code');
            $table->string('account_number');
            $table->string('reconciliation_account');
            $table->string('payment_term');
            $table->string('payment_block');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_company_codes');
    }
};
