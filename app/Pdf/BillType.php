<?php

namespace App\Pdf;

use Illuminate\Support\Collection;

class BillType implements PdfRepository
{

    public string $filename;

    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
    }

    public function getSubject(): string
    {
        return 'Rechnung';
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

}
