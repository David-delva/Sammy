<?php

declare(strict_types=1);

namespace App\Support\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

final class PdfRenderer
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function stream(
        string $view,
        array $data,
        string $filename,
        string $paper = 'a4',
        string $orientation = 'portrait',
    ): Response {
        return Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation)
            ->stream($filename);
    }
}
