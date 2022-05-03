<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->on('categories')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->on('shops')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->text('description');
            $table->double('price', 8, 2);
            $table->string('image');
            $table->string('ProductImage')->nullable();
            $table->dropColumn('image');
            $table->string('brand');
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
        Schema::dropIfExists('products');
    }
}
