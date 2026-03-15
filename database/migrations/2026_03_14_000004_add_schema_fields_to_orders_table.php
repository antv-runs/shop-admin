<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable()->after('id');
            }

            if (!Schema::hasColumn('orders', 'currency')) {
                $table->char('currency', 3)->default('USD')->after('total_amount');
            }

            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0)->after('currency');
            }

            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal_amount');
            }

            if (!Schema::hasColumn('orders', 'delivery_fee_amount')) {
                $table->decimal('delivery_fee_amount', 10, 2)->default(0)->after('discount_amount');
            }

            if (!Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code')->nullable()->after('delivery_fee_amount');
            }

            if (!Schema::hasColumn('orders', 'placed_at')) {
                $table->timestamp('placed_at')->nullable()->after('promo_code');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_number')) {
                $table->unique('order_number');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_number')) {
                $table->dropUnique(['order_number']);
            }

            if (Schema::hasColumn('orders', 'placed_at')) {
                $table->dropColumn('placed_at');
            }

            if (Schema::hasColumn('orders', 'promo_code')) {
                $table->dropColumn('promo_code');
            }

            if (Schema::hasColumn('orders', 'delivery_fee_amount')) {
                $table->dropColumn('delivery_fee_amount');
            }

            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }

            if (Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->dropColumn('subtotal_amount');
            }

            if (Schema::hasColumn('orders', 'currency')) {
                $table->dropColumn('currency');
            }

            if (Schema::hasColumn('orders', 'order_number')) {
                $table->dropColumn('order_number');
            }
        });
    }
};
