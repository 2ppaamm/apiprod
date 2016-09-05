<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_name');
            $table->string('site_shortname');
            $table->string('main_color');
            $table->string('email')->unique()->nullable();
            $table->integer('number_of_teaching_days');
            $table->string('site_url');
            $table->string('site_logo');
            $table->integer('no_rights_to_pass')->default(2);
            $table->integer('no_wrongs_to_fail')->default(2);
            $table->boolean('self_paced')->default(TRUE);
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
        Schema::drop('configs');
    }
}
