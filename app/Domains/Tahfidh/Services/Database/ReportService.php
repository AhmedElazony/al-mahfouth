<?php

namespace App\Domains\Tahfidh\Services\Database;

use App\Domains\Tahfidh\Models\Report;
use App\Domains\Tahfidh\Services\Contracts\ReportService as ReportServiceContract;
use App\Support\Services\Database\BaseService;

class ReportService extends BaseService implements ReportServiceContract
{
    public function __construct()
    {
        parent::__construct(Report::class);
	}
}