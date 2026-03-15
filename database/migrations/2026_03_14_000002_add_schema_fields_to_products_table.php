<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'details')) {
                $table->text('details')->nullable()->after('description');
            }

            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('products', 'currency')) {
                $table->char('currency', 3)->default('USD')->after('original_price');
            }

            if (!Schema::hasColumn('products', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->nullable()->after('currency');
            }

            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('rating_avg');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('products', 'rating_avg')) {
                $table->dropColumn('rating_avg');
            }

            if (Schema::hasColumn('products', 'currency')) {
                $table->dropColumn('currency');
            }

            if (Schema::hasColumn('products', 'original_price')) {
                $table->dropColumn('original_price');
            }

            if (Schema::hasColumn('products', 'details')) {
                $table->dropColumn('details');
            }
        });
    }
};
