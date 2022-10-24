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
        Schema::create('retirements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cash_advance_id')->unsigned();
            $table->foreign('cash_advance_id')->references('id')->on('cash_advances')->onDelete('cascade');
            $table->dateTime('starts')->nullable();
            $table->dateTime('ends')->nullable();
            $table->text('description');
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
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
        Schema::dropIfExists('retirements');
    }
};
