<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'name')) {
                $table->string('name')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('orders', 'email')) {
                $table->string('email')->nullable()->after('name');
            }

            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });

        if (Schema::hasColumn('orders', 'user_id')) {
            $this->dropUserIdForeignKeyIfExists();

            $this->alterUserIdNullable(true);

            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        if (Schema::hasColumn('orders', 'user_id')) {
            $this->dropUserIdForeignKeyIfExists();

            $this->alterUserIdNullable(false);

            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('orders', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('orders', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('orders', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    private function alterUserIdNullable(bool $nullable): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(sprintf(
                'ALTER TABLE `orders` MODIFY `user_id` BIGINT UNSIGNED %s',
                $nullable ? 'NULL' : 'NOT NULL'
            ));

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(sprintf(
                'ALTER TABLE "orders" ALTER COLUMN "user_id" %s',
                $nullable ? 'DROP NOT NULL' : 'SET NOT NULL'
            ));

            return;
        }

        if ($driver === 'sqlsrv') {
            DB::statement(sprintf(
                'ALTER TABLE [orders] ALTER COLUMN [user_id] BIGINT %s',
                $nullable ? 'NULL' : 'NOT NULL'
            ));
        }
    }

    private function dropUserIdForeignKeyIfExists(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $connection->getDatabaseName())
                ->where('TABLE_NAME', 'orders')
                ->where('COLUMN_NAME', 'user_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');

            if ($foreignKey !== null) {
                DB::statement(sprintf(
                    'ALTER TABLE `orders` DROP FOREIGN KEY `%s`',
                    str_replace('`', '``', $foreignKey)
                ));
            }

            return;
        }

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $exception) {
            // Ignore when foreign key does not exist.
        }
    }
};
