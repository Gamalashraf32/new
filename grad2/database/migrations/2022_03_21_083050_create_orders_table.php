<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->on('shops')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('shop_user_id')->nullable();
            $table->foreign('shop_user_id')->on('users')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->double('subtotal_price', 10, 2)->default(0);
            $table->double('discounts', 10, 2)->default(0);
            $table->double('shipping_price', 10, 2)->default(0);
            $table->double('extra_shipping', 10, 2)->default(0);
            $table->double('user_balance', 10, 2)->default(0);
            $table->double('total_price', 10, 2)->default(0);
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
        Schema::dropIfExists('orders');
    }
}
