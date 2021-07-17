<?php

namespace App\Pdf;

use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface PdfRepository
{

    public function getSubject(): string;

    public function setFilename(string $filename): self;

    public function getFilename(): string;

    public function getView(): string;

    public function getTemplate(): string;

    public function getPositions(Collection $page): array;

    public function getFamilyName(Collection $page): string;

    public function getAddress(Collection $page): string;

    public function getZip(Collection $page): string;

    public function getLocation(Collection $page): string;

    public function createable(Member $member): bool;

    public function linkLabel(): string;

    public function getUntil(): Carbon;

}
