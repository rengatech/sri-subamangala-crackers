<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'url_slug')) {
                $table->string('url_slug')->nullable();
            }
            if (!Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title')->nullable();
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['url_slug', 'seo_title', 'description'];
            $existing = array_filter($columns, fn ($col) => Schema::hasColumn('products', $col));
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};