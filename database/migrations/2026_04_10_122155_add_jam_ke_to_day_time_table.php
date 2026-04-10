<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('day_time', function (Blueprint $table) {
            $table->integer('jam_ke')->nullable()->after('timeslot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_time', function (Blueprint $table) {
            $table->dropColumn('jam_ke');
        });
    }
};
