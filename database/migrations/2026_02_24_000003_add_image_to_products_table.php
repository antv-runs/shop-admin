<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // legacy migration kept for history; product images now live in product_images table
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no-op
    }
};
