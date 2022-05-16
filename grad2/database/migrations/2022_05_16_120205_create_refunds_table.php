<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->on('shops')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->on('orders')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default('Pending');
            $table->string('details')->nullable();
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('refunds');
    }
}
