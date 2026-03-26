<?php

namespace App\Http\Middleware;

use App\Services\AcademicYearService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyPastYear
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === 'admin') {
            return $next($request);
        }

        $service = app(AcademicYearService::class);

        if (! $service->isCurrentYear() && ! $request->isMethod('GET')) {
            return back()->with('error', "Action impossible : vous etes en mode consultation sur une annee differente de l'annee en cours.");
        }

        return $next($request);
    }
}
