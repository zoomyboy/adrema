<?php

namespace App\Tex;

use Illuminate\View\Compilers\BladeCompiler;

class TexCompiler extends BladeCompiler
{
    protected $contentTags = ['<<<', '>>>'];

    protected $rawTags = ['<<<!!', '!!>>>'];
}
