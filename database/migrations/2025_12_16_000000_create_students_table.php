<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->enum('educational_stage', ['no_school', 'primary_school', 'preparatory_school', 'secondary_school', 'university_stage', 'graduate']);
            $table->string('code')->unique();
            $table->date('begin_memorizing_at')->nullable();
            $table->date('memorizing_completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
