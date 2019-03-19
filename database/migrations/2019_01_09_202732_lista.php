<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Lista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('createList', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('origin',130);
            $table->string('destination',130);
            $table->string('date');
            $table->string('schedules');
            $table->string('conductor');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('membersOfList', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('userConfirm')->default(false);
            $table->boolean('ownerConfirm')->default(false);
            $table->unsignedInteger('list_id');
            $table->foreign('list_id')
                  ->references('id')->on('createList')
                  ->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
           
            $table->timestamps();
        });

        Schema::create('membersToConfirmOnList', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('list_id');
            $table->foreign('list_id')
                  ->references('id')->on('createList')
                  ->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
           
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
        Schema::dropIfExists('createList');
        Schema::dropIfExists('membersOfList');
        Schema::dropIfExists('membersToConfirmOnList');
    }
}
