<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtractedHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extracted_histories', function (Blueprint $table) {
            $table->id();
            $table->boolean('done')->default(0);
            $table->string('parent');
            $table->string('kid_id');
            $table->string('kid_name');
            $table->string('level');
            $table->string('date');
            $table->string('subject_id');
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
        Schema::dropIfExists('extracted_histories');
    }
}
