<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students', 'user_id')->cascadeOnDelete();
            $table->enum('student_status', ['commited', 'absent', 'uncommited']);
            $table->boolean('is_online')->default(false);
            $table->enum('memorizing_amount', ['less_than_5_pages', 'from_5_to_10_pages', 'from_10_to_15_pages', 'one_juz', 'more_than_one_juz']);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_student');
    }
};
