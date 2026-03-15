<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('order_items', 'image_url')) {
                $table->string('image_url')->nullable()->after('product_name');
            }

            if (!Schema::hasColumn('order_items', 'currency')) {
                $table->char('currency', 3)->default('USD')->after('total');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('order_items')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'currency')) {
                $table->dropColumn('currency');
            }

            if (Schema::hasColumn('order_items', 'image_url')) {
                $table->dropColumn('image_url');
            }

            if (Schema::hasColumn('order_items', 'product_name')) {
                $table->dropColumn('product_name');
            }
        });
    }
};
