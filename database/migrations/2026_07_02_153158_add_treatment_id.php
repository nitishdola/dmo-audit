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
        Schema::table('telephonic_audits', function (Blueprint $table) {
            // 1. Drop the existing foreign key constraint
            $table->dropForeign(['pmjay_audit_id']);
            
            // 2. Drop the column itself (optional, but recommended if you no longer need it)
            $table->dropColumn('pmjay_audit_id');

            // 3. Add the new unsigned big integer column
            // (Using constrained() assumes your primary key on pmjay_treatments is 'id')
            $table->foreignId('pmjay_treatment_id')
                  ->nullable() // Remove this if the field should be mandatory
                  ->after('id') // Optional: positions the column visually in the database
                  ->constrained('pmjay_treatments')
                  ->onDelete('cascade'); // Optional: adjust delete behavior as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telephonic_audits', function (Blueprint $table) {
            // 1. Reverse the addition: drop the new foreign key and column
            $table->dropForeign(['pmjay_treatment_id']);
            $table->dropColumn('pmjay_treatment_id');

            // 2. Reverse the deletion: re-add the old column and foreign key
            // Note: You might need to adjust the data type or constraints to match your original schema
            $table->foreignId('pmjay_audit_id')
                  ->nullable()
                  ->constrained('pmjay_audits'); // Assumes the original table was pmjay_audits
        });
    }
};
