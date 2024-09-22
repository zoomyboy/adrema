<?php

namespace App\Form\Models;

use App\Form\Data\FormConfigData;
use App\Lib\Editor\EditorData;
use Database\Factories\Form\Models\FormtemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property FormConfigData $config
 */
class Formtemplate extends Model
{
    /** @use HasFactory<FormtemplateFactory> */
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'config' => FormConfigData::class,
        'mail_top' => EditorData::class,
        'mail_bottom' => EditorData::class,
    ];
}
