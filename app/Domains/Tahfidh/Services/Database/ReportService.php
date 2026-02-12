<?php

namespace App\Domains\Tahfidh\Services\Database;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Models\Report;
use App\Domains\Tahfidh\Services\Contracts\ReportService as ReportServiceContract;
use App\Support\Enums\ResponseMessageEnum;
use App\Support\Services\Database\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportService extends BaseService implements ReportServiceContract
{
    public function __construct()
    {
        parent::__construct(Report::class);
    }

    public function create(array $data): Report
    {
        return parent::create([
            ...$data,
            'created_by' => auth()->id(),
        ]);
    }

    public function getByGroup(string $groupId, array $with = [], array $filters = [], array $columns = ['*']): Collection
    {
        return $this->get($with, array_merge($filters, ['group_id' => $groupId]), $columns);
    }

    public function paginateByGroup(string $groupId, array $with = [], array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->paginate($with, array_merge($filters, ['group_id' => $groupId]), $perPage, $columns);
    }

    public function showByGroup(string $groupId, string $reportId, array $with = [], array $columns = ['*'])
    {
        return $this->model()
            ->with($with)
            ->where('group_id', $groupId)
            ->where('id', $reportId)
            ->first($columns);
    }

    public function storeByGroup(Group $group, array $data): Report
    {
        return DB::transaction(function () use ($group, $data) {
            $student = $group->students()->firstWhere('student_id', $data['student_id']);
            if (! $student) {
                throw new \Exception(__(ResponseMessageEnum::STUDENT_NOT_IN_GROUP->value), Response::HTTP_NOT_FOUND);
            }

            return $group->reports()->create([
                'student_id' => $data['student_id'],
                'date' => $data['date'],
                'attendance_status' => $data['attendance_status'],
                'memorized_amount' => $data['memorized_amount'] ?? null,
                'grade' => $data['grade'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function updateByGroup(Group $group, Report $report, array $data): Report
    {
        return DB::transaction(function () use ($group, $report, $data) {
            $group->reports()->where('id', $report->id)->update([
                'date' => $data['date'] ?? $report->date,
                'attendance_status' => $data['attendance_status'] ?? $report->attendance_status,
                'memorized_amount' => $data['memorized_amount'] ?? $report->memorized_amount,
                'grade' => $data['grade'] ?? $report->grade,
                'notes' => $data['notes'] ?? $report->notes,
            ]);

            return $report->refresh();
        });
    }

    public function deleteByGroup(Group $group, Report $report): void
    {
        $group->reports()->where('id', $report->id)->delete();
    }
}
