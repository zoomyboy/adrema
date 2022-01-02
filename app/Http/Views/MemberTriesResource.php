<?php

namespace App\Http\Views;

use App\Member\MemberResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Member\Member
 */
class MemberTriesResource extends MemberResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'try_ends_at' => $this->getModel()->try_created_at->addWeeks(8)->format('d.m.Y'),
            'try_ends_at_human' => $this->getModel()->try_created_at->addWeeks(8)->diffForHumans(),
        ]);
    }
}
