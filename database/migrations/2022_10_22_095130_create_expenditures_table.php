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
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->bigInteger('sub_budget_head_id')->unsigned();
            $table->foreign('sub_budget_head_id')->references('id')->on('sub_budget_heads')->onDelete('cascade');
            $table->string('beneficiary')->nullable();
            $table->bigInteger('cash_advance_id')->default(0);
            $table->bigInteger('batch_id')->default(0);
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
            $table->decimal('approved_amount', $precision = 30, $scale = 2)->default(0);
            $table->text('description')->nullable();
            $table->text('additional_info')->nullable();
            $table->enum('type', ['cash-advance', 'retirement', 'other'])->default('cash-advance');
            $table->enum('payment_type', ['staff-payment', 'third-party'])->default('staff-payment');
            $table->enum('status', ['cleared', 'batched', 'queried', 'paid'])->default('cleared');
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
        Schema::dropIfExists('expenditures');
    }
};
