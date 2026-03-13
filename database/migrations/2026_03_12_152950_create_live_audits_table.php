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
        Schema::create('live_audits', function (Blueprint $table) {
            $table->id();

            // DMO who conducted this independent audit
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();

            // ── Section 1: Patient & Case Details ──
            $table->string('patient_name');
            $table->string('contact_number', 20)->nullable();
            $table->string('hospital_name');                      // free text — no FK, independent visit
            $table->string('pmjay_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('package_booked')->nullable();
            $table->string('treating_doctor')->nullable();
            $table->string('doctor_specialization')->nullable();
            $table->dateTime('admission_datetime')->nullable();
            $table->dateTime('discharge_datetime')->nullable();
            $table->enum('treatment_type', ['Surgical', 'Medical'])->nullable();

            // ── Section 2: On-Bed Photo (AI validated) ──
            $table->string('bed_photo_path');
            $table->decimal('bed_photo_latitude', 10, 7);
            $table->decimal('bed_photo_longitude', 10, 7);
            $table->string('bed_photo_address')->nullable();
            $table->timestamp('bed_photo_taken_at')->nullable();

            // AI results stored for audit trail
            $table->boolean('ai_bed_detected')->default(false);
            $table->boolean('ai_patient_detected')->default(false);
            $table->boolean('ai_pmjay_card_detected')->default(false);
            $table->unsignedTinyInteger('ai_face_count')->default(0);
            $table->json('ai_labels')->nullable();
            $table->json('ai_objects')->nullable();
            $table->string('ai_validation_message')->nullable();

            // ── Section 3: Patient ID Proof ──
            $table->enum('patient_id_collected', ['Yes', 'No', 'NA'])->nullable();
            $table->string('patient_id_remarks')->nullable();

            // ── Section 4: Clinical Interview ──
            $table->text('presenting_complaints')->nullable();
            $table->string('symptoms_duration')->nullable();
            $table->enum('referred_from_other', ['Yes', 'No', 'NA'])->nullable();
            $table->string('referred_from_name')->nullable();
            $table->dateTime('patient_admitted_when')->nullable();
            $table->enum('patient_still_admitted', ['Yes', 'No', 'NA'])->nullable();
            $table->string('patient_still_admitted_remarks')->nullable();
            $table->text('diagnostic_tests_done')->nullable();
            $table->enum('surgery_conducted', ['Yes', 'No', 'NA'])->nullable();
            $table->enum('surgery_scar_present', ['Yes', 'No', 'NA'])->nullable();
            $table->string('surgery_scar_remarks')->nullable();

            // ── Section 5: Money Charged ──
            $table->enum('money_charged', ['Yes', 'No', 'NA'])->nullable();
            $table->decimal('money_charged_amount', 10, 2)->nullable();
            $table->enum('receipt_available', ['Yes', 'No', 'NA'])->nullable();
            $table->string('receipt_path')->nullable();

            // ── Section 6: Previous Hospitalisation ──
            $table->enum('previous_hospitalisation', ['Yes', 'No', 'NA'])->nullable();
            $table->string('previous_hospitalisation_remarks')->nullable();

            // ── Section 7: Other ──
            $table->text('other_remarks')->nullable();

            $table->timestamps();
        });

        Schema::create('live_audit_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_audit_id')->constrained('live_audits')->cascadeOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_audit_attachments');
        Schema::dropIfExists('live_audits');
    }
};
