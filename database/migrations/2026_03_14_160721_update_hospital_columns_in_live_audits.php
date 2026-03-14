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
        Schema::table('live_audits', function (Blueprint $table) {
            // Drop hospital_name column
            $table->dropColumn('hospital_name');

            // Add foreign keys
            $table->unsignedBigInteger('hospital_id')->after('id');
            $table->unsignedBigInteger('district_id')->after('hospital_id');

            // Foreign key constraints
            $table->foreign('hospital_id')
                  ->references('id')
                  ->on('hospitals')
                  ->cascadeOnDelete();

            $table->foreign('district_id')
                  ->references('id')
                  ->on('districts')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_audits', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropForeign(['district_id']);

            // Drop columns
            $table->dropColumn(['hospital_id', 'district_id']);

            // Restore hospital_name
            $table->string('hospital_name')->nullable();
        });
    }
};
