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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('service_code')->nullable();
            $table->string('registration_no')->unique()->nullable();
            $table->string('tin_no')->unique()->nullable();
            $table->string('name');
            $table->string('label')->unique();
            $table->string('payment_code')->unique()->nullable();
            $table->string('code')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->enum('status', ['registered', 'verified', 'denied'])->default('registered');
            $table->enum('category', ['nigerian-owned', 'nigerian-company-owned-by-foreign-company', 'foreign-owned', 'government-ministry', 'government-parastatal'])->default('nigerian-owned');
            $table->enum('type', ['contractor', 'owner'])->default('contractor');
            $table->bigInteger('no_of_staff')->default(0);
            $table->bigInteger('score')->default(0);
            $table->boolean('blacklisted')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};
