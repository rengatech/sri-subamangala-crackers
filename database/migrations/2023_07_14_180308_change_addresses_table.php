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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('city_town')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('pin_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('city_town')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('pin_code')->nullable(false)->change();
        });
    }
};
