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
            $table->string('doctor_specialization')->nullable()->after('treating_doctor');

            $table->dateTime('admission_datetime')->nullable();
            $table->dateTime('discharge_datetime')->nullable();

            $table->string('treatment_type')->nullable();
            $table->string('diagnosis')->nullable();

            $table->string('indoor_register')->nullable();
            $table->text('indoor_register_remarks')->nullable();

            $table->string('ot_register')->nullable();
            $table->text('ot_register_remarks')->nullable();

            $table->string('lab_register')->nullable();
            $table->text('lab_register_remarks')->nullable();

            $table->string('ipd_complete')->nullable();
            $table->text('ipd_complete_remarks')->nullable();

            $table->string('ipd_aligns')->nullable();
            $table->text('ipd_aligns_remarks')->nullable();

            $table->string('ot_notes_available')->nullable();
            $table->text('ot_notes_available_remarks')->nullable();

            $table->string('ot_notes_complete')->nullable();
            $table->text('ot_notes_complete_remarks')->nullable();

            $table->string('ot_notes_align')->nullable();
            $table->text('ot_notes_align_remarks')->nullable();

            $table->string('pre_anaesthesia')->nullable();
            $table->text('pre_anaesthesia_remarks')->nullable();

            $table->string('nursing_notes_available')->nullable();
            $table->text('nursing_notes_available_remarks')->nullable();

            $table->string('nursing_notes_complete')->nullable();
            $table->text('nursing_notes_complete_remarks')->nullable();

            $table->string('doctor_notes_available')->nullable();
            $table->text('doctor_notes_available_remarks')->nullable();

            $table->string('doctor_notes_complete')->nullable();
            $table->text('doctor_notes_complete_remarks')->nullable();

            $table->string('progress_chart_available')->nullable();
            $table->text('progress_chart_available_remarks')->nullable();

            $table->string('progress_chart_complete')->nullable();
            $table->text('progress_chart_complete_remarks')->nullable();

            $table->string('treatment_chart_available')->nullable();
            $table->text('treatment_chart_available_remarks')->nullable();

            $table->string('treatment_chart_complete')->nullable();
            $table->text('treatment_chart_complete_remarks')->nullable();

            $table->string('monitoring_available')->nullable();
            $table->text('monitoring_available_remarks')->nullable();

            $table->text('overall_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropColumn([
                'doctor_specialization',
                'admission_datetime',
                'discharge_datetime',
                'treatment_type',
                'diagnosis',
                'indoor_register',
                'indoor_register_remarks',
                'ot_register',
                'ot_register_remarks',
                'lab_register',
                'lab_register_remarks',
                'ipd_complete',
                'ipd_complete_remarks',
                'ipd_aligns',
                'ipd_aligns_remarks',
                'ot_notes_available',
                'ot_notes_available_remarks',
                'ot_notes_complete',
                'ot_notes_complete_remarks',
                'ot_notes_align',
                'ot_notes_align_remarks',
                'pre_anaesthesia',
                'pre_anaesthesia_remarks',
                'nursing_notes_available',
                'nursing_notes_available_remarks',
                'nursing_notes_complete',
                'nursing_notes_complete_remarks',
                'doctor_notes_available',
                'doctor_notes_available_remarks',
                'doctor_notes_complete',
                'doctor_notes_complete_remarks',
                'progress_chart_available',
                'progress_chart_available_remarks',
                'progress_chart_complete',
                'progress_chart_complete_remarks',
                'treatment_chart_available',
                'treatment_chart_available_remarks',
                'treatment_chart_complete',
                'treatment_chart_complete_remarks',
                'monitoring_available',
                'monitoring_available_remarks',
                'overall_remarks'
            ]);
        });
    }
};
