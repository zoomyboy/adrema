<?php

namespace Tests\Unit;

use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

class EditorDataTest extends TestCase
{

    public function testItReplacesBlockContentWithList(): void
    {
        $data = EditorRequestFactory::new()->paragraphs(['{search}'])->toData();

        $data->replaceWithList('search', ['A', 'B']);

        $this->assertEquals('A', data_get($data->blocks, '0.data.items.0.content'));
    }
}
