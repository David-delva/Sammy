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
        $service = app(AcademicYearService::class);

        // Si ce n'est pas l'année en cours et qu'on tente une écriture
        if (!$service->isCurrentYear() && !$request->isMethod('GET')) {
            return back()->with('error', "Action impossible : Vous êtes en mode consultation sur une année passée ou future.");
        }

        return $next($request);
    }
}
