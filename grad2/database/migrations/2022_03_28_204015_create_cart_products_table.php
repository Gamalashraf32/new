<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->on('shops')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->foreign('cart_id')->on('carts')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('product_id');
            $table->string('variant1')->nullable();
            $table->string('variant2')->nullable();
            $table->string('product_name');
            $table->integer('quantity')->default(0);
            $table->double('price', 10, 2)->default(0);
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
        Schema::dropIfExists('cart_products');
    }
}
