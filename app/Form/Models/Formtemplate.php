<?php

namespace App\Form\Models;

use App\Form\Data\FormConfigData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property FormConfigData $config
 */
class Formtemplate extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'config' => FormConfigData::class,
    ];
}
