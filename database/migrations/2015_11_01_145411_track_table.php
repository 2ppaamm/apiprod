<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('track');
            $table->string('description')->nullable();
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('image')->nullable();
            $table->integer('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->integer('field_id')->unsigned();
            $table->foreign('field_id')->references('id')->on('fields');
            $table->integer('level_id')->unsigned();
            $table->foreign('level_id')->references('id')->on('levels');
            $table->timestamps();
        });

        Schema::create('track_track', function (Blueprint $table) {
            $table->integer('preReq_track_id')->unsigned();
            $table->foreign('preReq_track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->integer('track_id')->unsigned();
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->primary(['preReq_track_id','track_id']);
            $table->timestamps();
        });

        Schema::create('course_track', function (Blueprint $table) {
            $table->integer('course_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->integer('track_id')->unsigned();
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->float('track_order')->default(9999);
            $table->integer('number_of')->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->primary(['course_id','track_id']);
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
        Schema::drop('track_track');
        Schema::drop('course_track');
        Schema::drop('tracks');
    }
}
