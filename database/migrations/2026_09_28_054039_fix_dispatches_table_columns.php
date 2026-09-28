<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the missing column (skipped where it already exists)
        if (! Schema::hasColumn('dispatches', 'lr_screenshot_path')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->string('lr_screenshot_path')->nullable();
            });
        }

        // 2. Old NOT NULL columns block the new insert (it no longer sends them)
        if (Schema::hasColumn('dispatches', 'LR_number')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->string('LR_number')->nullable()->change();
            });
        }

        if (Schema::hasColumn('dispatches', 'transport')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->string('transport')->nullable()->change();
            });
        }

        // 3. Give status a default WITHOUT redefining its enum values
        if (Schema::hasColumn('dispatches', 'status')) {
            DB::statement("ALTER TABLE `dispatches` ALTER COLUMN `status` SET DEFAULT 'dispatched'");
        }
    }

    public function down(): void
    {
        // Intentionally empty: don't drop columns that may hold real data
    }
};