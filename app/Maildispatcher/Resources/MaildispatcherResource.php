<?php

namespace App\Maildispatcher\Resources;

use App\Lib\HasMeta;
use App\Maildispatcher\Models\Maildispatcher;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Resources\MailgatewayResource;
use App\Member\FilterScope;
use App\Member\Member;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Maildispatcher
 */
class MaildispatcherResource extends JsonResource
{
    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'gateway' => new MailgatewayResource($this->whenLoaded('gateway')),
            'gateway_id' => $this->gateway_id,
            'filter' => $this->filter,
            'id' => $this->id,
            'links' => [
                'edit' => route('maildispatcher.edit', ['maildispatcher' => $this->getModel()]),
                'update' => route('maildispatcher.update', ['maildispatcher' => $this->getModel()]),
                'delete' => route('maildispatcher.destroy', ['maildispatcher' => $this->getModel()]),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'links' => [
                'create' => route('maildispatcher.create'),
                'index' => route('maildispatcher.index'),
            ],
            'default_model' => [
                'name' => '',
                'gateway_id' => null,
                'filter' => FilterScope::from([])->toArray(),
            ],
            'members' => Member::ordered()->get()->map(fn ($member) => ['id' => $member->id, 'name' => $member->fullname]),
            'gateways' => Mailgateway::pluck('name', 'id'),
        ];
    }
}
