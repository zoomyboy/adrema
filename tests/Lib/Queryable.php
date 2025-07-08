<?php

namespace Tests\Lib;

trait Queryable {

    public function toBase64(): string
    {
        return base64_encode(rawurlencode(json_encode($this->create())));
    }
}
