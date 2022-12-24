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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->longText('description');
            $table->date('due_date');
            $table->date('completed_at')->nullable();
            $table->longText('remark')->nullable();
            $table->bigInteger('taskable_id')->unsigned();
            $table->string('taskable_type');
            $table->enum('priority', ['low', 'medium', 'high', 'very-high'])->default('low');
            $table->enum('status', ['pending', 'in-progress', 'completed', 'overdue'])->default('pending');
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
        Schema::dropIfExists('tasks');
    }
};
