<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referal_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('referred_by');
            $table->unsignedBigInteger('user_referred');
            $table->unsignedBigInteger('payment_id');
            $table->string('amount_earned');
            $table->string('status')->default('active');
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
        Schema::dropIfExists('referal_transactions');
    }
};
