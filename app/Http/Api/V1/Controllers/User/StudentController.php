<?php

namespace App\Http\Api\V1\Controllers\User;

use App\Domains\User\Services\Contracts\UserService;
use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Resources\User\StudentGroupResource;
use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\Request;

class StudentController extends ApiController
{
    public function __construct(protected UserService $userService) {}

    public function groups(Request $request)
    {
        try {
            return $this->success(
                __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
                StudentGroupResource::collection($this->userService->getStudentGroups())
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
