<?php

use App\Domains\Tahfidh\Enums\MemorizingAmountsEnum;
use App\Domains\Tahfidh\Enums\StudentStatusesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students', 'user_id')->cascadeOnDelete();
            $table->enum('student_status', StudentStatusesEnum::values())->nullable();
            $table->enum('memorizing_amount', MemorizingAmountsEnum::values());
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
