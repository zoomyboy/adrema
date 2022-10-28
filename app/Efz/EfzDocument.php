<?php

namespace App\Efz;

use App\Member\Member;
use App\Pdf\Sender;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class EfzDocument extends Document
{
    public string $name;
    public string $slug;
    public string $secondLine;
    public string $now;
    public Sender $sender;

    public function __construct(public Member $member)
    {
        $this->name = $member->fullname;
        $this->slug = $member->slug;
        $this->secondLine = "geb. am {$member->birthday->format('d.m.Y')}, wohnhaft in {$member->location}";
        $this->now = now()->format('d.m.Y');
        $this->sender = $member->toSender();
    }

    public function basename(): string
    {
        return "efz-fuer-{$this->slug}";
    }

    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }

    public function template(): Template
    {
        return Template::make('tex.templates.efz');
    }

    public function view(): string
    {
        return 'tex.efz';
    }
}
