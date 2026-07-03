<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructure_audits', function (Blueprint $table) {
            $table->json('additional_file_paths')->nullable()->after('banner_remarks');
            $table->json('additional_file_names')->nullable()->after('additional_file_paths');
        });
    }

    public function down(): void
    {
        Schema::table('infrastructure_audits', function (Blueprint $table) {
            $table->dropColumn(['additional_file_paths', 'additional_file_names']);
        });
    }
};
