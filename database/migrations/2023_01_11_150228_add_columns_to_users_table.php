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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('department_id')->default(0)->after('id');
            $table->bigInteger('grade_level_id')->default(0)->after('department_id');
            $table->string('staff_no')->unique()->nullable()->after('grade_level_id');
            $table->bigInteger('company_id')->default(0)->after('staff_no');
            $table->string('mobile')->unique()->nullable()->after('email');
            $table->string('designation')->nullable()->after('mobile');
            $table->text('location')->nullable()->after('designation');
            $table->date('dob')->nullable()->after('location');
            $table->date('date_joined')->nullable()->after('dob');
            $table->enum('type', ['permanent', 'contract', 'secondment', 'appointment', 'contractor', 'support', 'adhoc'])->default('permanent')->after('date_joined');
            $table->enum('status', ['in-service', 'retired', 'transfer', 'removed'])->default('in-service')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_id');
            $table->dropColumn('staff_no');
            $table->dropColumn('grade_level_id');
            $table->dropColumn('company_id');
            $table->dropColumn('mobile');
            $table->dropColumn('designation');
            $table->dropColumn('location');
            $table->dropColumn('dob');
            $table->dropColumn('date_joined');
            $table->dropColumn('type');
            $table->dropColumn('status');
        });
    }
};
