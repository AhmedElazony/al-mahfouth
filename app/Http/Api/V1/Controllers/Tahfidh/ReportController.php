<?php

namespace App\Http\Api\V1\Controllers\Tahfidh;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Models\Report;
use App\Domains\Tahfidh\Services\Contracts\ReportService;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Requests\Tahfidh\StoreReportRequest;
use App\Http\Api\V1\Requests\Tahfidh\UpdateReportRequest;
use App\Http\Api\V1\Resources\Tahfidh\ReportResource;
use App\Support\Enums\ResponseMessageEnum;

class ReportController extends ApiController
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Group $group)
    {
        try {
            $filters = request()->only([
                'per_page',
                'student_id',
                'date_from',
                'date_to',
            ]);
            $reports = $this->reportService
                ->paginateByGroup(
                    $group->id,
                    ['student'],
                    $filters,
                    $filters['per_page'] ?? 15
                );

            return $this->paginated(
                $reports,
                ReportResource::class
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage()
            );
        }
    }

    public function show(Group $group, Report $report)
    {
        try {
            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                ReportResource::make(
                    $this->reportService->showByGroup(
                        $group->id,
                        $report->id,
                        ['student', 'group', 'createdBy']
                    )
                )
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage()
            );
        }
    }

    public function store(StoreReportRequest $request, Group $group)
    {
        try {
            $report = $this->reportService
                ->storeByGroup($group, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::ADDED_SUCCESSFULLY->value),
                ReportResource::make($report->load('student'))
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage()
            );
        }
    }

    public function update(UpdateReportRequest $request, Group $group, Report $report)
    {
        try {
            $report = $this->reportService
                ->updateByGroup($group, $report, $request->validated());

            return $this->success(
                __(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value),
                ReportResource::make($report->load('student'))
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage()
            );
        }
    }

    public function destroy(Group $group, Report $report)
    {
        try {
            $this->reportService->deleteByGroup($group, $report);

            return $this->success(
                __(ResponseMessageEnum::DELETED_SUCCESSFULLY->value)
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage()
            );
        }
    }
}
