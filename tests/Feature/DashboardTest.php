<?php

namespace Tests\Feature;

use App\Dashboard\Blocks\Block;
use App\Dashboard\DashboardFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysBlock(): void
    {
        $this->withoutExceptionHandling();
        app(DashboardFactory::class)->purge();
        app(DashboardFactory::class)->register(ExampleBlock::class);

        $this->login()->loginNami();

        $response = $this->get('/');

        $this->assertInertiaHas(['class' => 'name'], $response, 'blocks.0.data');
        $this->assertInertiaHas('Example', $response, 'blocks.0.title');
        $this->assertInertiaHas('exa', $response, 'blocks.0.component');
    }
}

class ExampleBlock extends Block
{
    public function title(): string
    {
        return 'Example';
    }

    /**
     * @return array<string, string>
     */
    public function data(): array
    {
        return ['class' => 'name'];
    }

    public function component(): string
    {
        return 'exa';
    }
}
