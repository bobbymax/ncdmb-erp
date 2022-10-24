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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->string('staffId')->unique()->nullable();
            $table->bigInteger('gradeLevel')->default(0);
            $table->bigInteger('company_id')->default(0);
            $table->string('mobile')->unique()->nullable();
            $table->string('designation')->nullable();
            $table->text('location')->nullalble();
            $table->date('dob')->nullable();
            $table->date('date_joined')->nullable();
            $table->enum('type', ['permanent', 'contract', 'secondment', 'appointment', 'contractor', 'support', 'adhoc'])->default('permanent');
            $table->enum('status', ['in-service', 'retired', 'transfer', 'removed'])->default('in-service');
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
        Schema::dropIfExists('records');
    }
};
