<?php

use App\Domains\Tahfidh\Enums\AttendanceStatusesEnum;
use App\Domains\Tahfidh\Enums\GradesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'user_id')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->enum('attendance_status', AttendanceStatusesEnum::values());
            $table->text('memorized_amount');
            $table->enum('grade', GradesEnum::values());
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
