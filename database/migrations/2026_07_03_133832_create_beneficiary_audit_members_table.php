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
        Schema::create('beneficiary_audit_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_audit_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('pmjay_id_number')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('relationship')->nullable();
            $table->unsignedTinyInteger('sort_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_audit_members');
    }
};
