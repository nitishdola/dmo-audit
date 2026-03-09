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
            $table->string('policy_code')->after('case_id');
            $table->date('preauth_init_date')->after('policy_code');
            $table->string('ben_mobile_no')->after('preauth_init_date');
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
