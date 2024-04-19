@foreach ($content as $block)

@if ($block['type'] === 'paragraph')
{!! $block['data']['text'] !!}
@endif

@if ($block['type'] === 'heading' && data_get($block, 'data.level', 2) === 2)
## {!! data_get($block, 'data.text') !!}
@endif

@if ($block['type'] === 'heading' && data_get($block, 'data.level', 2) === 3)
### {!! data_get($block, 'data.text') !!}
@endif

@if ($block['type'] === 'heading' && data_get($block, 'data.level', 2) === 4)
#### {!! data_get($block, 'data.text') !!}
@endif

@if ($block['type'] === 'list' && data_get($block, 'data.style', 'unordered') === 'unordered')
{!! collect(data_get($block, 'data.items', []))->map(fn ($item) => '* '.$item['content'])->implode("\n") !!}
@endif

@if ($block['type'] === 'list' && data_get($block, 'data.style', 'unordered') === 'ordered')
{!! collect(data_get($block, 'data.items', []))->map(fn ($item) => '1. '.$item['content'])->implode("\n") !!}
@endif

@if ($block['type'] === 'alert')
<x-mail::panel :type="$block['data']['type']">{!! data_get($block, 'data.message') !!}</x-mail::panel>
@endif

@endforeach
