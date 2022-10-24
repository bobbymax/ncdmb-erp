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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->text('description');
            $table->bigInteger('percentage_completion')->default(0);
            $table->bigInteger('percentage_payment')->default(0);
            $table->bigInteger('period')->default(0);
            $table->enum('measure', ['days', 'weeks', 'months', 'years'])->default('months');
            $table->enum('status', ['pending', 'in-progress', 'overdue', 'completed'])->default('pending');
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
        Schema::dropIfExists('milestones');
    }
};
