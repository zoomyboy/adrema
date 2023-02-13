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
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class InitializeAction
{
    use AsAction;

    public string $commandSignature = 'initialize {--mglnr=} {--password=} {--group=}';

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

    private Api $api;

    public function handle(int $mglnr, string $password, int $groupId): void
    {
        foreach ($this->initializers as $initializer) {
            app($initializer)->handle($this->api);
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
        $this->api = Nami::login($request->input('mglnr'), $request->input('password'));

        if (!$this->api->hasGroup($request->input('group_id'))) {
            throw ValidationException::withMessages(['nami' => 'Gruppierung nicht gefunden.']);
        }

        $settings->mglnr = $request->input('mglnr');
        $settings->password = $request->input('password');
        $settings->default_group_id = $request->input('group_id');
        $settings->save();

        $this->handle(
            (int) $request->input('mglnr', 0),
            (string) $request->input('password', ''),
            (int) $request->input('group_id', 0)
        );

        return redirect()->route('home')->success('Initialisierung beauftragt. Wir benachrichtigen dich per Mail wenn alles fertig ist.');
    }

    public function asCommand(Command $command, NamiSettings $settings): void
    {
        $mglnr = (int) $command->option('mglnr');
        $password = $command->option('password');
        $group = (int) $command->option('group');
        $this->api = Nami::login($mglnr, $password);
        $settings->mglnr = $mglnr;
        $settings->password = $password;
        $settings->default_group_id = $group;
        $settings->save();
        $this->handle((int) $mglnr, (string) $password, (int) $group);
    }
}
