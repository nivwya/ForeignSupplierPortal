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

        Schema::create('vendor_contacts', function (Blueprint $table) {
            $table->string('contact_id')->primary();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->enum('contact_type', [""]);
            $table->string('contact_person', 100);
            $table->string('department');
            $table->string('phone');
            $table->string('fax')->nullable();
            $table->string('email');
            $table->string('mobile')->nullable();
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
        Schema::dropIfExists('vendor_contacts');
    }
};
