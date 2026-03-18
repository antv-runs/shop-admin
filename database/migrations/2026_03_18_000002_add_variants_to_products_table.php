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
        if (!Schema::hasColumn('products', 'colors')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('colors')->nullable()->after('details');
            });
        }

        if (!Schema::hasColumn('products', 'sizes')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('sizes')->nullable()->after('colors');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'sizes')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('sizes');
            });
        }

        if (Schema::hasColumn('products', 'colors')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('colors');
            });
        }
    }
};