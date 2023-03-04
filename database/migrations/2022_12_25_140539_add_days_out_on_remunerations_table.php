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
        Schema::table('remunerations', function (Blueprint $table) {
            $table->bigInteger('days_off')->default(0)->after('no_of_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('remunerations', function (Blueprint $table) {
            $table->dropColumn('days_off');
        });
    }
};
