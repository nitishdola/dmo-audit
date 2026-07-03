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
        Schema::create('beneficiary_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('pmjay_treatments')->cascadeOnDelete();

            // Patient Information
            $table->string('pmjay_family_id');
            $table->string('name');
            $table->string('guardian_name'); // Father's/Husband's name
            $table->text('address');
            $table->foreignId('district_id')->constrained('districts');
            $table->string('state');
            $table->string('pin_code');
            $table->string('contact_no');

            // General Information
            $table->string('ecard_made_at');
            $table->enum('ecard_charged', ['Yes', 'No']);
            $table->decimal('ecard_charge_amount', 10, 2)->nullable();
            $table->enum('availed_services', ['Yes', 'No']);
            $table->foreignId('hospital_id')->constrained('hospitals');
            $table->text('symptoms');
            $table->date('admission_date');
            $table->date('discharge_date');
            $table->unsignedInteger('days_hospitalized');
            $table->enum('free_food', ['Yes', 'No']);
            $table->text('treatment_given');
            $table->enum('surgery_scar', ['Yes', 'No', 'NA']);
            $table->text('surgery_scar_remarks')->nullable();

            // Photo match
            $table->enum('photo_match', ['Yes', 'No', 'NA']);

            // Remarks
            $table->text('other_remarks')->nullable();
            $table->text('recommendation');

            $table->foreignId('submitted_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_audits');
    }
};
