<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToHouses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('houses', function($table) {
            $table->integer('price');
            $table->string('currency')->default('SGD');
            $table->integer('underperform')->default(40);
            $table->integer('overperform')->default(90);            
        });        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('houses', function($table) {
            $table->dropColumn('price');
            $table->dropColumn('currency');
            $table->dropColumn('underperform');
            $table->dropColumn('overperform');
        });
    }
}
