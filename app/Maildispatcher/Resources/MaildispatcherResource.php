<?php

namespace App\Maildispatcher\Resources;

use App\Activity;
use App\Group;
use App\Lib\HasMeta;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Resources\MailgatewayResource;
use App\Member\FilterScope;
use App\Member\Member;
use App\Subactivity;
use Illuminate\Http\Resources\Json\JsonResource;

class MaildispatcherResource extends JsonResource
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
            'gateway' => new MailgatewayResource($this->whenLoaded('gateway')),
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
            'activities' => Activity::pluck('name', 'id'),
            'subactivities' => Subactivity::pluck('name', 'id'),
            'members' => Member::ordered()->get()->map(fn ($member) => ['id' => $member->id, 'name' => $member->fullname]),
            'groups' => Group::pluck('name', 'id'),
            'gateways' => Mailgateway::pluck('name', 'id'),
        ];
    }
}
