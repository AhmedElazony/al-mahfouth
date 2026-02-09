<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS statistics');
        DB::statement(<<<'SQL'
            CREATE VIEW statistics AS
            SELECT
				(SELECT COUNT(*) FROM groups) AS groups_count,
				(SELECT COUNT(*) FROM students) AS students_count,
				(SELECT COUNT(*) FROM teachers) AS teachers_count,
				(SELECT COUNT(*) FROM reports) AS reports_count
SQL);
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS statistics');
    }
};
