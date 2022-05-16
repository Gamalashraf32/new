<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('shop_owner_id')->nullable();
            $table->foreign('shop_owner_id')->on('shop_owners')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('currency')->default('egp');
            $table->string('swifter_domain')->nullable();
            $table->integer('has_discount')->default(1);
            $table->string('time_zone')->nullable();
            $table->string('slogan')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('address')->nullable();
            $table->integer('phone_number')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
