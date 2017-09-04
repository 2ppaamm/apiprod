<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGamification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamecodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gamecode');
            $table->string('description');
            $table->timestamps();
        });

        Schema::table('questions', function($table) {
            $table->integer('gamecodes_id')->unsigned()->nullable();
            $table->foreign('gamecodes_id')->references('id')->on('gamecodes');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function($table) {
            $table->dropForeign('questions_gamecodes_id_foreign');
            $table->dropColumn('gamecodes_id');
        });

        Schema::drop('gamecodes');
    }
}
