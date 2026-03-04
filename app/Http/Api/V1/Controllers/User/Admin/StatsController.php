<?php

namespace App\Http\Api\V1\Controllers\User\Admin;

use App\Domains\Tahfidh\Models\Group;
use App\Http\Api\V1\Controllers\ApiController;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends ApiController
{
    public function stats(Request $request)
    {
        try {
            $stats = DB::table('statistics')->first();

            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                $stats
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function showGroupStats(Group $group)
    {
        try {
            $groupStats = DB::table('group_stats')
                ->where('group_id', $group->id)
                ->first();

            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                $groupStats
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }
}
