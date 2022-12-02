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
    public function up(): void
    {
        Schema::create('joinings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('training_id')->unsigned();
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->bigInteger('learning_category_id')->unsigned();
            $table->foreign('learning_category_id')->references('id')->on('learning_categories')->onDelete('cascade');
            $table->bigInteger('qualification_id')->unsigned();
            $table->foreign('qualification_id')->references('id')->on('qualifications')->onDelete('cascade');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->string('facilitator')->nullable();
            $table->string('location')->nullable();
            $table->enum('category', ['virtual', 'on-premise'])->default('on-premise');
            $table->enum('type', ['nomination', 'archive'])->default('archive');
            $table->enum('resident', ['international', 'local'])->default('local');
            $table->string('certificate')->nullable();
            $table->enum('status', ['registered', 'ongoing', 'completed', 'verified'])->default('registered');
            $table->boolean('attended')->default(false);
            $table->boolean('isArchived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('joinings');
    }
};
