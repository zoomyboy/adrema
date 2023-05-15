<?php

namespace App\Initialize\Actions;

use App\Initialize\InitializeActivities;
use App\Initialize\InitializeConfessions;
use App\Initialize\InitializeCountries;
use App\Initialize\InitializeCourses;
use App\Initialize\InitializeFees;
use App\Initialize\InitializeGenders;
use App\Initialize\InitializeGroups;
use App\Initialize\InitializeMembers;
use App\Initialize\InitializeNationalities;
use App\Initialize\InitializeRegions;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class InitializeAction
{
    use AsAction;

    /**
     * @var array<int, class-string>
     */
    public array $initializers = [
        InitializeGroups::class,
        InitializeNationalities::class,
        InitializeFees::class,
        InitializeActivities::class,
        InitializeConfessions::class,
        InitializeCountries::class,
        InitializeGenders::class,
        InitializeRegions::class,
        InitializeCourses::class,
        InitializeMembers::class,
    ];

    public function handle(): void
    {
        $api = app(NamiSettings::class)->login();

        foreach ($this->initializers as $initializer) {
            app($initializer)->handle($api);
        }
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric',
            'password' => 'required|string',
            'group_id' => 'required|numeric',
            'params' => 'required|array',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'group_id' => 'Gruppierungsnr',
        ];
    }

    public function asController(ActionRequest $request, NamiSettings $settings): RedirectResponse
    {
        $settings->mglnr = (int) $request->input('mglnr');
        $settings->password = $request->input('password');
        $settings->default_group_id = (int) $request->input('group_id');
        $settings->search_params = $request->input('params');
        $settings->save();
        self::dispatch();

        return redirect()->route('home');
    }
}
