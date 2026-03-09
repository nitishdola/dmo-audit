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
        Schema::create('pmjay_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pmjay_treatment_id')->constrained()->cascadeOnDelete();

            $table->foreignId('district_id')->constrained();

            $table->enum('audit_type', [
                'telephonic',
                'field'
            ]);

            $table->enum('status', [
                'pending',
                'completed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmjay_audits');
    }
};
