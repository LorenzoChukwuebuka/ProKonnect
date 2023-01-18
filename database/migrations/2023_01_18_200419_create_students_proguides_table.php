<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *i
     * @return void
     */
    public function up()
    {
        Schema::create('students_proguides', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->foreignId()->index();
            $table->unsignedInteger('proguide_id')->foreignId()->index();
            $table->softDeletes();
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
        Schema::dropIfExists('students_proguides');
    }
};
