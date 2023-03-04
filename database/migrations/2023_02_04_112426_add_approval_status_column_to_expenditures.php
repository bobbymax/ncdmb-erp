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
            $table->enum('approval_status', ['pending', 'cleared', 'queried', 'resolved', 'posted'])->default('pending')->after('status');
            $table->enum('stage', ['budget-office', 'treasury', 'audit'])->default('budget-office')->after('approval_status');
            $table->text('remark')->nullable()->after('description');
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
            $table->dropColumn('approval_status');
            $table->dropColumn('stage');
            $table->dropColumn('remark');
        });
    }
};
