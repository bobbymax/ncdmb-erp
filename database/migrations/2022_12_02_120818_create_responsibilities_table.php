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
        Schema::create('responsibilities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->bigInteger('pillar_id')->unsigned();
            $table->foreign('pillar_id')->references('id')->on('pillars')->onDelete('cascade');
            $table->longText('description');
            $table->date('due_date');
            $table->date('completed_at')->nullable();
            $table->longText('remark')->nullable();
            $table->enum('expectation', ['ns', 'dm', 'm', 'e'])->default('ns');
            $table->enum('priority', ['low', 'medium', 'high', 'very-high'])->default('low');
            $table->enum('status', ['pending', 'in-progress', 'completed', 'overdue'])->default('pending');
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
        Schema::dropIfExists('responsibilities');
    }
};
