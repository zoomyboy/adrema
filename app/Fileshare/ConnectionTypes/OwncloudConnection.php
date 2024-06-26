<?php

namespace App\Fileshare\ConnectionTypes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OwncloudConnection extends ConnectionType
{

    public function __construct(
        public string $user,
        public string $password,
        public string $baseUrl,
    ) {
    }

    public function check(): bool
    {
        try {
            $response = Http::withoutVerifying()->withBasicAuth($this->user, $this->password)->acceptJson()->get($this->baseUrl . '/ocs/v1.php/cloud/capabilities?format=json');
            return $response->ok();
        } catch (ConnectionException $e) {
            return false;
        }
    }
}
