<?php

namespace App\Form;

use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;
use Lorisleiva\Actions\ActionRequest;

class FormSettings extends LocalSettings implements Storeable
{
    public string $registerUrl;
    public string $clearCacheUrl;
    public ?string $replyToMail;

    public static function group(): string
    {
        return 'form';
    }

    public static function title(): string
    {
        return 'Veranstaltungen';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'registerUrl' => 'present|string',
            'clearCacheUrl' => 'present|string',
            'replyToMail' => 'nullable|string|email',
        ];
    }

    public function beforeSave(ActionRequest $request): void
    {
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => [
                'data' => [
                    'registerUrl' => $this->registerUrl,
                    'clearCacheUrl' => $this->clearCacheUrl,
                    'replyToMail' => $this->replyToMail,
                ]
            ]
        ];
    }
}
