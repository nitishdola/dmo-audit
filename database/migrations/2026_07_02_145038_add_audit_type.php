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
        Schema::table('pmjay_treatments', function (Blueprint $table) {
            $table->string('audit_type')->after('patient_district_id')->nullable();
            $table->string('status')->after('audit_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmjay_treatments', function (Blueprint $table) {
            //
        });
    }
};
