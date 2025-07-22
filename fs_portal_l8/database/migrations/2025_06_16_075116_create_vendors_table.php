<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code')->unique();
            $table->string('vendor_name');
            $table->bigInteger('authorization_group')->nullable();
            $table->bigInteger('account_group')->nullable();
            $table->timestamps(); // Only this, not manual date fields
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
