<?php

namespace App;

use App\Nami\HasNamiField;
use Database\Factories\GenderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasNamiField;

    /** @use HasFactory<GenderFactory> */
    use HasFactory;

    public $fillable = ['name', 'nami_id'];

    public function getSalutationAttribute(): string
    {
        return match ($this->name) {
            'Männlich' => 'Herr',
            'Weiblich' => 'Frau',
            default => ''
        };
    }

    public function getShortAttribute(): string
    {
        return match ($this->name) {
            'Männlich' => 'm',
            'Weiblich' => 'w',
            default => ''
        };
    }

    public static function fromString(string $title): self
    {
        return self::firstWhere('name', $title);
    }
}
