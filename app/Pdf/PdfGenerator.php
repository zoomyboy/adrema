<?php

namespace App\Pdf;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Str;
use Storage;

class PdfGenerator implements Responsable
{

    private ?string $filename = null;
    private PdfRepository $repo;
    private string $dir;

    public function setRepository(PdfRepository $repo): self
    {
        $this->repo = $repo;

        return $this;
    }

    public function render(): self
    {
        $this->filename = $this->repo->getFilename();
        $this->dir = Str::random(32);

        Storage::disk('temp')->put($this->dir.'/'.$this->repo->getFilename().'.tex', $this->compileView());
        Storage::disk('temp')->makeDirectory($this->dir);

        $this->copyTemplateTo(Storage::disk('temp')->path($this->dir));

        $command = 'cd '.Storage::disk('temp')->path($this->dir);
        $command .= ' && '.env('XELATEX').' --halt-on-error '.$this->repo->getFilename().'.tex';
        exec($command, $output, $returnVar);

        return $this;
    }

    public function compileView(): string
    {
        return (string) view()->make($this->repo->getView(), [
            'data' => $this->repo,
        ]);
    }

    public function toResponse($request)
    {
        return response()->file($this->getCompiledFilename(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$this->filename}.pdf\"",
        ]);
    }

    public function getCompiledFilename(): string
    {
        return Storage::disk('temp')->path($this->dir.'/'.$this->filename.'.pdf');
    }

    private function copyTemplateTo(string $destination): void
    {
        $templatePath = resource_path("views/tex/templates/{$this->repo->getTemplate()}");
        exec('cp '.$templatePath.'/* '.$destination);
    }

}
