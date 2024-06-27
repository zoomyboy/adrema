<?php

namespace App\Fileshare\Resources;

use App\Fileshare\ConnectionTypes\ConnectionType;
use App\Fileshare\Models\FileshareConnection;
use App\Lib\HasMeta;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FileshareConnection
 */
class FileshareConnectionResource extends JsonResource
{

    use HasMeta;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'is_active' => $this->type->check(),
            'type' => get_class($this->type),
            'config' => $this->type->toArray(),
            'id' => $this->id,
            'type_human' => $this->type::title(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'default' => [
                'name' => '',
                'type' => null,
                'config' => null,
            ],
            'types' => ConnectionType::forSelect(),
            'links' => [
                'store' => route('fileshare.store'),
            ]
        ];
    }
}
