<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorBankTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_bank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->string('bank_country', 5);
            $table->string('bank_key', 20);
            $table->string('bank_account', 50)->unique();
            $table->string('bank_control_key', 10);
            $table->string('partner_bank_type', 10);
            $table->string('collection_authorization', 20);
            $table->string('reference_details', 100);
            $table->string('account_holder');
            $table->string('account_description');
            $table->string('status_bk_details_hd', 50);
            $table->date('valid_from');
            $table->date('eff_to');
            $table->boolean('is_active');
            $table->timestamps(); // or keep manual date columns if needed
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_bank');
    }
}
