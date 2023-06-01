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
        ];
    }
}
