<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorContactsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_contacts', function (Blueprint $table) {
            $table->string('contact_id')->primary();
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->enum('contact_type', ['PRIMARY', 'BILLING', 'SHIPPING', 'TECHNICAL']); // <-- Set your actual types
            $table->string('contact_person', 100);
            $table->string('department');
            $table->string('phone');
            $table->string('fax')->nullable();
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_contacts');
    }
}

