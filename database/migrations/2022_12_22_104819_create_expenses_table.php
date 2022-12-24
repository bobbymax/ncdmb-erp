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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('claim_id')->unsigned();
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
            $table->bigInteger('remuneration_id')->unsigned();
            $table->foreign('remuneration_id')->references('id')->on('remunerations')->onDelete('cascade');
            $table->bigInteger('remuneration_child_id')->default(0);
            $table->date('from');
            $table->date('to')->nullable();
            $table->text('description');
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
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
        Schema::dropIfExists('expenses');
    }
};
