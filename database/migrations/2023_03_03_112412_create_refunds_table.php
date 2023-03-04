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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->bigInteger('expenditure_id')->unsigned();
            $table->foreign('expenditure_id')->references('id')->on('expenditures')->onDelete('cascade');
            $table->bigInteger('sub_budget_head_id')->default(0);
            $table->string('beneficiary')->nullable();
            $table->decimal('amount', $precision=30, $scale=2)->default(0);
            $table->longText('description')->nullable();
            $table->longText('remark')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->boolean('closed')->default(false);
            $table->bigInteger('budget_year')->default(0);
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
        Schema::dropIfExists('refunds');
    }
};
