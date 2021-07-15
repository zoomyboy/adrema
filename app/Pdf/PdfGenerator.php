<?php

namespace App\Pdf;

use Illuminate\Contracts\Support\Responsable;
use Storage;

class PdfGenerator implements Responsable
{

    private ?string $filename = null;

    public function render(PdfRepository $repo): self
    {
        $content = view()->make('pdf.pdf', [
            'data' => $repo,
        ]);

        $filename = $repo->getFilename();
        dd($filename);

        Storage::disk('temp')->put($repo->getBasename(), $content);
    }

    public function toResponse($request)
    {
        return response()->file($this->filename);
    }

}

