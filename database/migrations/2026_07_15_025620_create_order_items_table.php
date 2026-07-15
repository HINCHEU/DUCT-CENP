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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('duct_type_id')->constrained('duct_types')->cascadeOnDelete();
            $table->json('dimensions');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('quantity_delivered')->default(0);
            $table->decimal('surface_area', 10, 3)->default(0);
            $table->string('fabrication_status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
