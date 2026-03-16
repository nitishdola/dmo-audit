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
        Schema::create('infrastructure_audits', function (Blueprint $table) {
            $table->id();

            // ── Audit meta ──────────────────────────────────────────────────
            $table->foreignId('submitted_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('investigation_date');

            // ── A. Hospital Details ─────────────────────────────────────────
            $table->string('hospital_name');
            $table->text('hospital_address');
            $table->string('hospital_id')->nullable();
            $table->enum('hospital_type', ['Public', 'Private']);
            $table->unsignedInteger('pmjay_beneficiaries_tms')->nullable();
            $table->unsignedInteger('pmjay_beneficiaries_actual')->nullable();

            // ── B. Hospital Infrastructure ──────────────────────────────────

            // Existence & Registration
            $table->enum('hospital_existence',      ['Yes', 'No', 'NA'])->nullable();
            $table->string('hospital_existence_remarks')->nullable();

            $table->enum('hospital_response', ['Co-operative', 'Non Co-operative', 'Indifferent'])->nullable();
            $table->string('hospital_response_remarks')->nullable();

            $table->enum('dghs_registered',         ['Yes', 'No', 'NA'])->nullable();
            $table->string('dghs_registered_remarks')->nullable();

            // AI Banner verification
            $table->string('banner_photo_path')->nullable();
            $table->boolean('ai_banner_pass')->nullable();
            $table->boolean('ai_pmjay_branding')->nullable();
            $table->boolean('ai_banner_visible')->nullable();
            $table->string('ai_banner_summary')->nullable();
            $table->text('ai_banner_details')->nullable();
            $table->string('banner_remarks')->nullable();

            // PMAM Kiosk
            $table->enum('pmam_kiosk_available',    ['Yes', 'No', 'NA'])->nullable();
            $table->enum('pmam_kiosk_location',     ['Easily Visible', 'Far Inside', 'Not Available'])->nullable();
            $table->string('pmam_kiosk_remarks')->nullable();

            $table->enum('promo_boards_displayed',  ['Yes', 'No', 'NA'])->nullable();
            $table->string('promo_boards_remarks')->nullable();

            // Beds
            $table->unsignedSmallInteger('total_beds')->nullable();
            $table->unsignedSmallInteger('general_ward_beds')->nullable();
            $table->enum('bed_distance_adequate',   ['Yes', 'No', 'NA'])->nullable();
            $table->string('bed_distance_remarks')->nullable();

            // HDU
            $table->enum('hdu_available',           ['Yes', 'No', 'NA'])->nullable();
            $table->unsignedSmallInteger('hdu_beds')->nullable();

            // ICU
            $table->enum('icu_available',           ['Yes', 'No', 'NA'])->nullable();
            $table->unsignedSmallInteger('icu_beds')->nullable();
            $table->enum('icu_well_equipped',       ['Yes', 'No', 'NA'])->nullable();
            $table->json('icu_equipment')->nullable();   // {A:'Yes', B:'No', ...}
            $table->string('icu_equipment_remarks')->nullable();

            // OT
            $table->enum('ot_available',            ['Yes', 'No', 'NA'])->nullable();
            $table->unsignedSmallInteger('ot_count')->nullable();
            $table->unsignedSmallInteger('ot_tables')->nullable();
            $table->enum('ot_sterilization',        ['Yes', 'No', 'NA'])->nullable();
            $table->string('ot_sterilization_remarks')->nullable();
            $table->enum('ot_lighting',             ['Yes', 'No', 'NA'])->nullable();
            $table->enum('ot_ac',                   ['Yes', 'No', 'NA'])->nullable();
            $table->enum('ot_well_equipped',        ['Yes', 'No', 'NA'])->nullable();
            $table->json('ot_equipment')->nullable();    // {A:'Yes', B:'No', ...}
            $table->string('ot_equipment_remarks')->nullable();

            // Diagnostics & Hygiene
            $table->enum('pathology_diagnostics',   ['Inhouse', 'Out sourced', 'Not Available'])->nullable();
            $table->string('pathology_remarks')->nullable();
            $table->enum('biomedical_waste',        ['Yes', 'No', 'NA'])->nullable();
            $table->string('biomedical_waste_remarks')->nullable();
            $table->enum('overall_hygiene',         ['Good', 'Average', 'Poor'])->nullable();
            $table->string('overall_hygiene_remarks')->nullable();
            $table->text('infra_other_remarks')->nullable();

            // ── C. Human Resource ───────────────────────────────────────────
            $table->enum('pmam_available',          ['Yes', 'No', 'NA'])->nullable();
            $table->string('pmam_available_remarks')->nullable();
            $table->enum('onduty_doctors',          ['Yes', 'No', 'NA'])->nullable();
            $table->json('onduty_doctor_types')->nullable(); // {A:'Yes', B:'No', C:'NA'}
            $table->string('onduty_doctors_remarks')->nullable();
            $table->enum('adequate_nurses',         ['Yes', 'No', 'NA'])->nullable();
            $table->string('adequate_nurses_remarks')->nullable();
            $table->enum('nurses_qualified',        ['Yes', 'No', 'NA'])->nullable();
            $table->string('nurses_qualified_remarks')->nullable();
            $table->enum('technicians_available',   ['Yes', 'No', 'NA'])->nullable();
            $table->enum('pharmacists_available',   ['Yes', 'No', 'NA'])->nullable();
            $table->enum('specialists_available',   ['Yes', 'No', 'NA'])->nullable();
            $table->string('specialists_remarks')->nullable();
            $table->text('hr_other_remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('investigation_date');
            $table->index('hospital_type');
            $table->index('submitted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructure_audits');
    }
};
