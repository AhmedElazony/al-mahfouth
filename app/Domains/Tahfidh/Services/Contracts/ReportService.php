<?php

namespace App\Domains\Tahfidh\Services\Contracts;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Models\Report;
use App\Support\Services\Contracts\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ReportService extends BaseService
{
    public function getByGroup(string $groupId, array $with = [], array $filters = [], array $columns = ['*']): Collection;

    public function paginateByGroup(string $groupId, array $with = [], array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function showByGroup(string $groupId, string $reportId, array $with = [], array $columns = ['*']);

    public function storeByGroup(Group $group, array $data): Report;

    public function updateByGroup(Group $group, Report $report, array $data): Report;

    public function deleteByGroup(Group $group, Report $report): void;
}
