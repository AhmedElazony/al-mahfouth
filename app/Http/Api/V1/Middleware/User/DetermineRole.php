<?php

namespace App\Http\Api\V1\Middleware\User;

use App\Support\Enums\ResponseMessageEnum;
use App\Support\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetermineRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponse::error(
                __(ResponseMessageEnum::UNAUTHORIZED->value),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userRole = $user->role instanceof UserRolesEnum
            ? $user->role->value
            : $user->role;

        if (! in_array($userRole, $roles, true)) {
            return ApiResponse::error(
                __(ResponseMessageEnum::FORBIDDEN->value),
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
