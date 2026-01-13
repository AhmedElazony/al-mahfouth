<?php

namespace App\Http\Api\V1\Controllers\Tahfidh;

use App\Domains\Tahfidh\Models\Group;
use App\Domains\Tahfidh\Services\Contracts\GroupServiceInterface;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Resources\Tahfidh\GroupResource;
use App\Support\Enums\ResponseMessageEnum;
use App\Support\Traits\WithPagination;

class GroupController extends ApiController
{
    use WithPagination;

    public function __construct(
        protected GroupServiceInterface $groupService,
    ) {}

    public function index()
    {
        try {
            $perPage = request()->query('per_page', 15);
            $groups = $this->groupService->get($perPage);

            return $this->paginated(
                data: $groups,
                resource: GroupResource::class,
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function show(Group $group)
    {
        try {
            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                GroupResource::make($group),
            );
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                $th->getCode() !== 0 ? $th->getCode() : 500
            );
        }
    }

    public function store() {}
}
