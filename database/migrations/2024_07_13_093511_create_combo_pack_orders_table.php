<?php

use App\Models\Customer;
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
        Schema::create('combo_pack_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId(Customer::class)->nullable();
            $table->foreignId('delivery_location_id')->constrained('addresses')->onDelete('cascade');
            $table->decimal('net_total');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_pack_orders');
    }
};
