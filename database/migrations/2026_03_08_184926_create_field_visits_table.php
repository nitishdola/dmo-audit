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
        Schema::create('field_visits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('audit_id');

            // case details
            $table->string('patient_name')->nullable();
            $table->string('package_booked')->nullable();
            $table->string('treating_doctor')->nullable();

            // observations
            $table->string('lama')->nullable();
            $table->text('lama_remarks')->nullable();

            $table->string('outdoor_register')->nullable();
            $table->text('outdoor_register_remarks')->nullable();

            $table->string('ipd_available')->nullable();
            $table->text('ipd_available_remarks')->nullable();

            $table->string('discharge_summary')->nullable();
            $table->text('discharge_summary_remarks')->nullable();

            $table->text('other_remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_visits');
    }
};
