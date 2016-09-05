<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Mycurrentresult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mycurrentresults', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('current_skill_id')->unsigned()->default(1);
            $table->foreign('current_skill_id')->references('id')->on('skills');
            $table->integer('current_difficulty_id')->unsigned()->default(1);
            $table->foreign('current_difficulty_id')->references('id')->on('difficulties');
            $table->integer('current_track_id')->unsigned()->default(1);
            $table->foreign('current_track_id')->references('id')->on('tracks');
            $table->decimal('total_maxile');
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
        Schema::drop('mycurrentresults');
    }
}
