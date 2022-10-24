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
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('code')->unique();
            $table->text('description');
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
            $table->decimal('spent_amount', $precision = 30, $scale = 2)->default(0);
            $table->date('starts')->nullable();
            $table->date('ends')->nullable();
            $table->enum('status', ['pending', 'raised', 'paid', 'retired'])->default('pending');
            $table->boolean('closed')->default(false);
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
        Schema::dropIfExists('cash_advances');
    }
};
