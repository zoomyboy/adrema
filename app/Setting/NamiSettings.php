<?php

namespace App\Setting;

use App\Group;
use App\Initialize\Actions\NamiLoginCheckAction;
use App\Setting\Contracts\Storeable;
use Lorisleiva\Actions\ActionRequest;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class NamiSettings extends LocalSettings implements Storeable
{
    public string $mglnr;

    public string $password;

    public int $default_group_id;

    /** @var array<string, string> */
    public array $search_params;

    public static function group(): string
    {
        return 'nami';
    }

    public function login(): Api
    {
        return Nami::login($this->mglnr, $this->password);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required',
            'password' => 'required',
            'default_group_id' => 'required',
        ];
    }

    public function beforeSave(ActionRequest $request): void
    {
        NamiLoginCheckAction::run([
            'mglnr' => $request->mglnr,
            'password' => $request->password,
        ]);
    }

    public function localGroup(): ?Group
    {
        return Group::firstWhere('nami_id', $this->default_group_id);
    }

    public static function title(): string
    {
        return 'NaMi-Login';
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => [
                'mglnr' => $this->mglnr,
                'password' => '',
                'default_group_id' => $this->default_group_id,
            ]
        ];
    }
}
