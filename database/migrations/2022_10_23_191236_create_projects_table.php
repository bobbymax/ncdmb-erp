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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_category_id')->unsigned();
            $table->foreign('service_category_id')->references('id')->on('service_categories')->onDelete('cascade');
            $table->bigInteger('procurement_method_id')->unsigned();
            $table->foreign('procurement_method_id')->references('id')->on('procurement_methods')->onDelete('cascade');
            $table->bigInteger('department_id')->default(0);
            $table->text('title');
            $table->string('code')->unique();
            $table->string('lot_number')->unique()->nullable();
            $table->text('location')->nullable();
            $table->string('coordinates')->nullable();
            $table->bigInteger('period')->default(0);
            $table->decimal('boq', $precision=30, $scale=2)->default(0);
            $table->string('threshold')->nullable();
            $table->string('stage')->nullable();
            $table->string('champion')->nullable();
            $table->enum('measure', ['days', 'weeks', 'months', 'years'])->default('months');
            $table->enum('status', ['pending', 'in-progress', 'overdue', 'completed'])->default('pending');
            $table->bigInteger('year')->default(0);
            $table->boolean('closed')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('projects');
    }
};
