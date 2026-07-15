<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->after('id');
            $table->string('priority')->default('normal')->after('status');
            $table->text('notes')->nullable()->after('priority');
            $table->date('requested_delivery_date')->nullable()->after('notes');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('total_area', 10, 3)->default(0)->after('surface_area');
            $table->string('thickness')->default('0.6')->after('total_area');
            $table->boolean('canvas_flange')->default(false)->after('thickness');
            $table->boolean('inner_strut')->default(false)->after('canvas_flange');
            $table->text('remarks')->nullable()->after('inner_strut');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['total_area', 'thickness', 'canvas_flange', 'inner_strut', 'remarks']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'priority', 'notes', 'requested_delivery_date']);
        });
    }
};
