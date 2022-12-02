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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->bigInteger('classification_id')->unsigned();
            $table->foreign('classification_id')->references('id')->on('classifications')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('title');
            $table->string('label')->unique();
            $table->longText('description')->nullable();
            $table->bigInteger('quantity_expected')->default(0);
            $table->bigInteger('quantity_received')->default(0);
            $table->decimal('amount', $precision=30, $scale=2)->default(0);
            $table->dateTime('end_of_life')->nullable();
            $table->boolean('inStock')->default(true);
            $table->boolean('isDistributable')->default(false);
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
        Schema::dropIfExists('products');
    }
};
