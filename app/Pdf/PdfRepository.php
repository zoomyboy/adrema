<?php

namespace App\Pdf;

use Illuminate\Support\Collection;

interface PdfRepository
{

    public function getSubject(): string;

    public function setFilename(string $filename): self;

    public function getFilename(): string;

}
