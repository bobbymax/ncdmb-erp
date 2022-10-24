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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label')->unique();
            $table->bigInteger('slots')->default(1);
            $table->dateTime('start')->nullable();
            $table->dateTime('expire')->nullable();
            $table->enum('type', ['roles', 'groups'])->default('roles');
            $table->boolean('no_expiration')->default(false);
            $table->boolean('isSuper')->default(false);
            $table->boolean('isActive')->default(true);
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
        Schema::dropIfExists('roles');
    }
};
