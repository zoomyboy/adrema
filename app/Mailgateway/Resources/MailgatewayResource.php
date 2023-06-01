<?php

namespace App\Mailgateway\Resources;

use App\Lib\HasMeta;
use App\Mailgateway\Models\Mailgateway;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Mailgateway
 */
class MailgatewayResource extends JsonResource
{
    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'domain' => $this->domain,
            'type_human' => $this->type::name(),
            'works' => $this->type->works(),
        ];
    }

    public static function meta(): array
    {
        return [
            'links' => [
                'store' => route('api.mailgateway.store'),
            ],
            'types' => app('mail-gateways')->map(fn ($gateway) => [
                'id' => $gateway,
                'name' => $gateway::name(),
                'fields' => $gateway::fields(),
                'defaults' => (object) $gateway::defaults(),
            ])->prepend([
                'id' => null,
                'name' => '-- kein --',
                'fields' => [],
                'defaults' => (object) [],
            ]),
            'default' => [
                'domain' => '',
                'name' => '',
                'type' => [
                    'params' => [],
                    'cls' => null,
                ],
            ],
        ];
    }
}
