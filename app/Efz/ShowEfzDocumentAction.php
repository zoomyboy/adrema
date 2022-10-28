<?php

namespace App\Efz;

use App\Member\Member;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class ShowEfzDocumentAction
{
    use AsAction;

    public function handle(Member $member): BaseCompiler
    {
        return Tex::compile(new EfzDocument($member));
    }

    public function asController(Member $member, ActionRequest $request): BaseCompiler
    {
        return $this->handle($member);
    }
}
