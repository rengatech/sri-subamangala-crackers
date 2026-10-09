<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'products'    => ['price'],
            'orders'      => ['net_total', 'discount_total', 'sub_total'],
            'order_items' => ['total'],
        ];

        foreach ($columns as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    Schema::table($table, function (Blueprint $t) use ($col) {
                        $t->decimal($col, 10, 2)->nullable()->change();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Intentionally left empty: reverting to integer would drop decimals.
    }
};
