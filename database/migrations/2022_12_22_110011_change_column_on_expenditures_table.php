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
            $table->dropColumn('cash_advance_id');
            $table->bigInteger('claim_id')->default(0)->after('beneficiary');
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
            $table->dropColumn('claim_id');
            $table->bigInteger('cash_advance_id')->default(0)->after('beneficiary');
        });
    }
};
