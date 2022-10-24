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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label')->unique();
            $table->string('code')->unique()->nullable();
            $table->string('icon')->nullable();
            $table->string('path')->nullable();
            $table->string('url')->unique()->nullable();
            $table->bigInteger('parentId')->default(0);
            $table->enum('type', ['application', 'module', 'page'])->default('page');
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
        Schema::dropIfExists('modules');
    }
};
