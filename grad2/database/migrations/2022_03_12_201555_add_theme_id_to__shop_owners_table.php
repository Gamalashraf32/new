<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThemeIdToShopOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_owners', function (Blueprint $table) {
            $table->unsignedBigInteger('theme_id')->nullable();
            $table->foreign('theme_id')->on('themes')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_owners', function (Blueprint $table) {
            //
        });
    }
}
