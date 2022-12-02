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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('commitment_id')->unsigned();
            $table->foreign('commitment_id')->references('id')->on('commitments')->onDelete('cascade');
            $table->longText('objectives')->nullable();
            $table->longText('measure')->nullable();
            $table->bigInteger('weight')->default(0);
            $table->bigInteger('target')->default(0);
            $table->longText('remark')->nullable();
            $table->enum('expectation', ['ns', 'dm', 'm', 'e'])->default('ns');
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
        Schema::dropIfExists('targets');
    }
};
