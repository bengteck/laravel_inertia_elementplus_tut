<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->datetime('date');
            $table->string('parent');
            $table->bigInteger('kid_id');
            $table->string('kid_name');
            $table->string('level');
            $table->bigInteger('subject_id');
            $table->string('subject');
            $table->string('type')->nullable();
            $table->string('unit')->nullable();
            $table->string('section')->nullable();
            $table->string('duration')->nullable();
            $table->string('score')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('progress');
    }
}
