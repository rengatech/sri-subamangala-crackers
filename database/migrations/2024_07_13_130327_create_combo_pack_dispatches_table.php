<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ComboPackOrders;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('combo_pack_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ComboPackOrders::class);
            $table->string('LR_number');
            $table->string('transport');
            $table->date('book_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['dispatched']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_pack_dispatches');
    }
};
