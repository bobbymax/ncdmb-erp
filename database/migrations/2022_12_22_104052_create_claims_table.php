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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('title');
            $table->string('reference_no')->unique();
            $table->decimal('total_amount', $precision = 30, $scale = 2)->default(0);
            $table->decimal('spent_amount', $precision = 30, $scale = 2)->default(0);
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->enum('type', ['staff-claim', 'touring-advance'])->default('staff-claim');
            $table->enum('status', ['pending', 'raised', 'registered', 'unregistered', 'cleared', 'batched', 'queried', 'paid'])->default('pending');
            $table->boolean('retired')->default(false);
            $table->boolean('paid')->default(false);
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
        Schema::dropIfExists('claims');
    }
};
