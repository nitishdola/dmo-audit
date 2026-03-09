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
        Schema::table('field_visits', function (Blueprint $table) {
            $table->string('photo_path')->after('other_remarks');
            $table->string('photo_latitude')->after('photo_path');
            $table->string('photo_longitude')->after('photo_latitude');
            $table->string('photo_address')->after('photo_longitude');
            $table->dateTime('photo_taken_at')->after('photo_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            //
        });
    }
};
