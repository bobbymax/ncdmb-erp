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
        Schema::create('sub_budget_heads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('budget_head_id')->unsigned();
            $table->foreign('budget_head_id')->references('id')->on('budget_heads')->onDelete('cascade');
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->string('code')->unique()->nullable();
            $table->string('name');
            $table->string('label')->unique();
            $table->enum('type', ['capital', 'recurrent', 'personnel'])->default('capital');
            $table->enum('status', ['active', 'closed', 'blocked'])->default('active');
            $table->boolean('logistics')->default(false);
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
        Schema::dropIfExists('sub_budget_heads');
    }
};
