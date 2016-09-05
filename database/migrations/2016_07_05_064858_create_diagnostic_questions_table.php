<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosticQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnostic_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_id')->unsigned()->nullable();
            $table->foreign('field_id')->references('id')->on('fields')->onDelete('set null');
            $table->integer('user_id')->unsigned()->default(1);
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('question');
            $table->string('question_image')->nullable();
            $table->string('answer0')->nullable();
            $table->string('answer0_image')->nullable();
            $table->integer('answer0_skill_id')->unsigned()->nullable();
            $table->foreign('answer0_skill_id')->references('id')->on('skills')->onDelete('set null');
            $table->string('answer1')->nullable();
            $table->string('answer1_image')->nullable();
            $table->integer('answer1_skill_id')->unsigned()->nullable();
            $table->foreign('answer1_skill_id')->references('id')->on('skills')->onDelete('set null');
            $table->string('answer2')->nullable();
            $table->string('answer2_image')->nullable();
            $table->integer('answer2_skill_id')->unsigned()->nullable();
            $table->foreign('answer2_skill_id')->references('id')->on('skills')->onDelete('set null');
            $table->string('answer3')->nullable();
            $table->string('answer3_image')->nullable();
            $table->integer('answer3_skill_id')->unsigned()->nullable();
            $table->foreign('answer3_skill_id')->references('id')->on('skills')->onDelete('set null');
            $table->integer('type_id')->unsigned()->default(1);
            $table->foreign('type_id')->references('id')->on('types');
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
        Schema::drop('diagnostic_questions');
    }
}
