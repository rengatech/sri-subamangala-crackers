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
        Schema::table('combo_packs', function (Blueprint $table) {
            // Check if the column exists before adding it
            if (!Schema::hasColumn('combo_packs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combo_packs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
