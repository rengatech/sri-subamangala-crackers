<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            $table->string('meta_template_id')->nullable()->after('id');
            $table->string('category')->default('UTILITY')->after('language_code');
            $table->json('components')->nullable()->after('category');
            $table->string('quality_score')->nullable()->after('status');
            $table->string('rejected_reason')->nullable()->after('quality_score');
        });

        // Change status from enum to string to support all Meta statuses
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            $table->string('status')->default('PENDING')->change();
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            $table->dropColumn(['meta_template_id', 'category', 'components', 'quality_score', 'rejected_reason']);
        });
    }
};
