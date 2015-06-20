<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_constants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('group');
            $table->softDeletes();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('uri_prefix')->unique();
            $table->string('description')->nullable();
            $table->string('datetime_format')->nullable();
            $table->softDeletes();
        });

        Schema::create('language_constant_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('constant_id')->unsigned();
            $table->integer('language_id')->unsigned();

            $table->foreign('constant_id')->references('id')->on('language_constants')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['constant_id', 'language_id']);
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('language_constants');
        Schema::drop('languages');
        Schema::drop('language_constant_values');
    }
}
