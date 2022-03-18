<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_owners', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('email');
            $table->string('password');
            $table->integer('phone_number');
            $table->string('site_name');
            $table->string('site_address');
            $table->string('country');
            $table->string('government');
            $table->string('city');
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->on('plans')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('shop_owners');
    }
}
