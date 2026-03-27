<?php

namespace App\Http\Middleware;

use App\Services\AcademicWriteAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcademicWriteAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $service = app(AcademicWriteAccessService::class);

        if ($service->canManageSelectedYear($request->user())) {
            return $next($request);
        }

        abort(403, $service->denialMessage());
    }
}
