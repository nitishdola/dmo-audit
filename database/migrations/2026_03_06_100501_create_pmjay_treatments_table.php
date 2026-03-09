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
        Schema::create('pmjay_treatments', function (Blueprint $table) {
            $table->id();

            $table->string('registration_id')->unique();
            $table->string('case_id')->nullable();

            $table->string('patient_name')->nullable();
            $table->string('member_id')->nullable();

            $table->foreignId('hospital_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('procedure_code')->nullable();
            $table->text('procedure_details')->nullable();

            $table->string('category_details')->nullable();

            $table->decimal('amount_preauth_approved',12,2)->nullable();
            $table->decimal('amount_claim_paid',12,2)->nullable();

            $table->string('case_status')->nullable();

            $table->dateTime('admission_dt')->nullable();
            $table->dateTime('discharge_dt')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmjay_treatments');
    }
};
