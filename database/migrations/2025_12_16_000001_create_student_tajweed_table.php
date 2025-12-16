<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_tajweed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->primary()->constrained('students', 'user_id')->cascadeOnDelete();
            $table->enum('recitation_level', ['excellent', 'good_plus', 'good', 'bad']);
            $table->enum('tajweed_learning_status', ['not_yet', 'in_progress', 'completed']);
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
