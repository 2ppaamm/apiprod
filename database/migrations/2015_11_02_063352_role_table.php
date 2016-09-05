<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('permission');
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['permission_id','role_id']);
            $table->timestamps();
        });

      //  Schema::create('role_user', function (Blueprint $table) {
        //    $table->integer('role_id')->unsigned();
          //  $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            //$table->integer('user_id')->unsigned();
     //       $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
       //     $table->timestamps();
         //   $table->primary(['role_id', 'user_id']);
       // });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::drop('role_user');
        Schema::drop('permission_role');
        Schema::drop('roles');
        Schema::drop('permissions');
    }
}
