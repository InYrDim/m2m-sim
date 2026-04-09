<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->string('teacher_code')->nullable()->after('lesson_id');
        });

        // Migrate data if existing
        DB::table('timetables')
            ->join('teachers', 'timetables.teacher_id', '=', 'teachers.id')
            ->update(['timetables.teacher_code' => DB::raw('teachers.code')]);

        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->foreign('teacher_code')->references('code')->on('teachers')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_id')->nullable()->after('lesson_id');
        });

        // Migrate data back
        DB::table('timetables')
            ->join('teachers', 'timetables.teacher_code', '=', 'teachers.code')
            ->update(['timetables.teacher_id' => DB::raw('teachers.id')]);

        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['teacher_code']);
            $table->dropColumn('teacher_code');
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnUpdate()->nullOnDelete();
        });
    }
};
