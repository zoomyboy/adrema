<?php

namespace App;

use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasNamiField;
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
}
