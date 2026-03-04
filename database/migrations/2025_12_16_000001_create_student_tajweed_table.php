<?php

use App\Domains\Tahfidh\Enums\GradesEnum;
use App\Domains\Tahfidh\Enums\LearningStatusesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_tajweed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'user_id')->cascadeOnDelete();
            $table->enum('recitation_level', GradesEnum::values())->nullable();
            $table->enum('learning_status', LearningStatusesEnum::values())->nullable();
            // TODO: add tuhfetul_atfal_memorized, and jazrieah_memorized columns later
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_tajweed');
    }
};
