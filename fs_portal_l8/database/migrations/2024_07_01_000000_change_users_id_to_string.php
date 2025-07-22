<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeUsersIdToString extends Migration
{
    public function up()
    {
        // 1. Add a new string column for the new ID
        Schema::table('users', function (Blueprint $table) {
            $table->string('new_id', 32)->nullable()->after('id');
        });

        // 2. Copy old IDs to new_id as zero-padded strings (10 digits)
        DB::statement("UPDATE users SET new_id = LPAD(id, 10, '0')");

        // 3. Drop the old primary key and id column
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });

        // 4. Rename new_id to id and set as primary key
        Schema::table('users', function (Blueprint $table) {
            $table->string('id', 32)->primary();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('new_id');
        });
    }

    public function down()
    {
        // Not implemented: would require restoring integer IDs
    }
} 