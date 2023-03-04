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
        Schema::table('expenditures', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `expenditures` CHANGE `stage` `stage` ENUM('budget-office','treasury','audit','accounts') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'budget-office';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('expenditures', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `expenditures` CHANGE `stage` `stage` ENUM('budget-office','treasury','audit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'budget-office';");
        });
    }
};
