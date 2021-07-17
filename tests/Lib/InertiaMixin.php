<?php

namespace Tests\Lib;

use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

class InertiaMixin {

    public function assertInertia() {
        return function($component, $props, $key = null) {
            PHPUnit::assertEquals($component, $this->viewData('page')['component']);

            $this->assertInertiaHas($props, $key);

            return $this;
        };
    }

    public function assertInertiaComponent() {
        return function($component) {
            PHPUnit::assertEquals($component, $this->viewData('page')['component']);

            return $this;
        };
    }

    public function assertInertiaHasShared() {
        return function($bindings, $key = null) {
            $bindings = json_decode(json_encode($bindings), true);

            $viewData = json_decode(json_encode(
                data_get($this->viewData('page'), $key)
            ), true);

            $this->assertDeepNest($bindings, $viewData);
        };
    }

    public function assertInertiaHas() {
        return function($bindings, $key = null) {
            $bindings = json_decode(json_encode($bindings), true);

            $viewData = json_decode(json_encode(
                data_get($this->viewData('page')['props'], $key)
            ), true);

            $bindings = is_array($bindings) ? $bindings : [$bindings];
            $viewData = is_array($viewData) ? $viewData : [$viewData];

            $this->assertDeepNest($bindings, $viewData);
        };
    }

    public function assertDeepNest() {
        return function($should, $is) {
            foreach ($should as $key => $value) {
                PHPUnit::assertArrayHasKey($key, $is);

                if (is_array($value)) {
                    $this->assertDeepNest($value, $is[$key]);
                    continue;
                }

                PHPUnit::assertSame($value, $is[$key]);
            }
        };
    }

    public function assertInertiaHasItem() {

        return function($should, $nestedKey) {
            $is = data_get($this->viewData('page')['props'], $nestedKey);
            $is = collect(json_decode(json_encode($is), true));

            $should = collect(json_decode(json_encode($should), true));

            $has = $is->contains(function($isItem) use ($should) {
                return $this->isDeepEqual($should, Collection::wrap($isItem));
            });

            PHPUnit::assertTrue($has, 'Failed asserting that inertia attribute '.$nestedKey.' has Data '.print_r($should, true));

            return $this;
        };
        
    }

    public function inertia() {
        return function($item) {
            return data_get($this->viewData('page')['props'], $item);
        };
    }

    public function assertInertiaEquals() {
        return function($should, $nestedKey) {
            $is = data_get($this->viewData('page')['props'], $nestedKey);

            PHPUnit::assertSame($should, $is);

            return $this;
        };
    }

    public function ddp() {
        return function ($value) {
            dd(data_get($this->viewData('page'), $value));
        };
    }

    public function ddi() {
        return function ($value) {
            dd(data_get($this->viewData('page')['props'], $value));
        };
    }

    public function isDeepEqual() {
        return function (Collection $subset, Collection $compare) {
            $subset = $subset->filter(fn($item) => !is_array($item));
            $compare = $compare->filter(fn($item) => !is_array($item));

            return $subset->diffAssoc($compare)->isEmpty();
        };
    }

}

