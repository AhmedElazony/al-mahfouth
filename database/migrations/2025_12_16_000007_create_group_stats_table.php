<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS group_stats');
        DB::statement(<<<'SQL'
            CREATE VIEW group_stats AS
            SELECT
                g.id AS group_id,
                COUNT(gs.id) AS students_count,
                SUM(CASE WHEN gs.student_status = 'commited' THEN 1 ELSE 0 END) AS commited_count,
                SUM(CASE WHEN gs.student_status = 'uncommited' THEN 1 ELSE 0 END) AS uncommited_count,
                SUM(CASE WHEN gs.student_status = 'absent' THEN 1 ELSE 0 END) AS absent_count,
                SUM(CASE WHEN s.is_active = false THEN 1 ELSE 0 END) AS inactive_count
            FROM groups g
            LEFT JOIN group_student gs ON g.id = gs.group_id
            LEFT JOIN students s ON gs.student_id = s.id
            GROUP BY g.id
        SQL);
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS group_stats');
    }
};
