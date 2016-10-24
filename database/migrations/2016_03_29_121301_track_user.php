<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrackUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_user', function (Blueprint $table) {
            $table->integer('field_id')->unsigned();
            $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('field_maxile')->default(0);
            $table->dateTime('field_test_date')->nullable();
            $table->integer('month_achieved')->nullable();
            $table->primary(['field_id','user_id','month_achieved']);
            $table->timestamps();
        });
        Schema::create('track_user', function (Blueprint $table) {
            $table->integer('track_id')->unsigned();
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('track_maxile', 6,2)->default(0);
            $table->boolean('track_passed')->default(false);
            $table->dateTime('track_test_date')->nullable();
            $table->primary(['track_id','user_id']);
            $table->timestamps();
        });
        Schema::create('skill_user', function (Blueprint $table) {
            $table->integer('skill_id')->unsigned();
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('skill_maxile', 6,2)->default(0);
            $table->dateTime('skill_test_date')->default(date('Y-m-d'));
            $table->integer('skill_passed')->default(0);
            $table->integer('difficulty_passed')->default(0);
            $table->integer('noOfTries')->default(0);
            $table->integer('noOfPasses')->default(0);
            $table->integer('noOfFails')->default(0);
            $table->primary(['skill_id','user_id']);
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
        Schema::drop('skill_user');
        Schema::drop('track_user');
        Schema::drop('field_user');
    }
}
