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

        Schema::create('vendor_audit_log', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors');
            $table->string('table_name', 50);
            $table->enum('auction_type', [""]);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('changed_by');
            $table->dateTime('change_timestamp');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_audit_log');
    }
};
