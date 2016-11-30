<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToEnrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('house_role_user', function($table) {
            $table->decimal('amount_paid', 8,2)->nullable();
            $table->string('currency_code')->nullable();
            $table->string('payment_status')->nullable();
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('house_role_user', function($table) {
            $table->dropColumn('amount_paid');
            $table->dropColumn('currency_code');
            $table->dropColumn('payment_status');
        });
    }
}
