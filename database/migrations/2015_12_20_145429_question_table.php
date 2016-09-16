<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('skill_id')->unsigned()->nullable();
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('set null');
            $table->integer('difficulty_id')->unsigned()->nullable();
            $table->foreign('difficulty_id')->references('id')->on('difficulties')->onDelete('set null');
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('question');
            $table->string('question_image')->nullable();
            $table->string('answer0')->nullable();
            $table->string('answer0_image')->nullable();
            $table->string('answer1')->nullable();
            $table->string('answer1_image')->nullable();
            $table->string('answer2')->nullable();
            $table->string('answer2_image')->nullable();
            $table->string('answer3')->nullable();
            $table->string('answer3_image')->nullable();
            $table->integer('correct_answer');
            $table->integer('status_id')->unsigned()->default(1);
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->text('source')->nullable();
            $table->integer('type_id')->unsigned()->default(1);
            $table->foreign('type_id')->references('id')->on('types');
            $table->timestamps();
        });

        Schema::create('question_user', function (Blueprint $table) {
            $table->integer('question_id')->unsigned();
            $table->foreign('question_id')->references('id')->on('questions');
            $table->integer('test_id')->unsigned()->default(1);
            $table->foreign('test_id')->references('id')->on('tests');
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('question_answered')->default(false);
            $table->boolean('correct')->default(false);
            $table->datetime('answered_date')->nullable();
            $table->integer('attempts')->default(0);
            $table->primary(['question_id','user_id', 'test_id']);
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
        Schema::drop('question_user');
        Schema::drop('questions');
    }
}
