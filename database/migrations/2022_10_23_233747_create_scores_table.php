<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('evaluation_id')->unsigned();
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->text('requirement');
            $table->enum('pattern', ['scoring', 'sighting'])->default('sighting');
            $table->bigInteger('value')->default(0);
            $table->enum('sighting', ['na', 'sighted', 'not-sighted'])->default('na');
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
        Schema::dropIfExists('scores');
    }
};
