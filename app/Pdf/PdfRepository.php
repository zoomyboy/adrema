<?php

namespace App\Pdf;

interface PdfRepository
{
    public function setFilename(string $filename): static;

    public function getFilename(): string;

    public function getView(): string;

    public function getTemplate(): ?string;

    public function getScript(): EnvType;
}
