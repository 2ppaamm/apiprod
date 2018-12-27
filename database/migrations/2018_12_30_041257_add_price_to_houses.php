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
            $table->integer('framework_id')->unsigned()->after('id')->default(1);
            $table->foreign('framework_id')->references('id')->on('frameworks');
            $table->integer('start_framework')->default(0);
            $table->integer('end_framework')->default(1300);                        
        }); 
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
            $table->dropForeign(['framework_id']);
            $table->dropColumn('framework_id');
            $table->dropColumn('start_framework');
            $table->dropColumn('end_framework');
        });
    }
}
