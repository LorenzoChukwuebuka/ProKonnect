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
        Schema::create('proguide_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id')->foreignId()->index();
            $table->unsignedInteger('proguide_id')->foreignId()->index();
            $table->string('note')->nullable();
            $table->string('rating')->nullable();
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
        Schema::dropIfExists('proguide_ratings');
    }
};
