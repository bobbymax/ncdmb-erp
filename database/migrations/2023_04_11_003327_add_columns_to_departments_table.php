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
        Schema::table('departments', function (Blueprint $table) {
            $table->bigInteger('bco')->default(0)->after('type');
            $table->bigInteger('bo')->default(0)->after('bco');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('bco');
            $table->dropColumn('bo');
        });
    }
};
