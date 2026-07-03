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
        Schema::create('medical_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('audit_id')
                ->constrained('pmjay_treatments')
                ->cascadeOnDelete();

            $table->string('patient_name')->nullable();
            $table->string('package_booked')->nullable();
            $table->string('treating_doctor')->nullable();
            $table->string('doctor_specialization')->nullable();

            $table->dateTime('admission_datetime')->nullable();
            $table->dateTime('discharge_datetime')->nullable();

            $table->enum('treatment_type',['Surgical','Medical'])->nullable();
            $table->string('diagnosis')->nullable();

            $fields = [
                'lama',
                'outdoor_register',
                'indoor_register',
                'ot_register',
                'lab_register',
                'ipd_complete',
                'ipd_aligns',
                'ot_notes_available',
                'ot_notes_complete',
                'ot_notes_align',
                'pre_anaesthesia',
                'nursing_notes_available',
                'nursing_notes_complete',
                'doctor_notes_available',
                'doctor_notes_complete',
                'progress_chart_available',
                'progress_chart_complete',
                'treatment_chart_available',
                'treatment_chart_complete',
                'monitoring_available',
                'discharge_summary',
            ];

            foreach ($fields as $field) {
                $table->enum($field,['Yes','No','NA'])->nullable();
                $table->text($field.'_remarks')->nullable();
            }

            $table->longText('overall_remarks')->nullable();

            $table->foreignId('submitted_by')
                ->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_audits');
    }
};
