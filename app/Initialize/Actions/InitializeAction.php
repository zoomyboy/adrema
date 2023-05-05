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

    public function asController(ActionRequest $request): RedirectResponse
    {
        $api = Nami::freshLogin($request->input('mglnr'), $request->input('password'));

        if (!$api->hasGroup($request->input('group_id'))) {
            throw ValidationException::withMessages(['nami' => 'Gruppierung nicht gefunden.']);
        }

        $this->setApi((int) $request->input('mglnr'), $request->input('password'), (int) $request->input('group_id'));
        self::dispatch();

        return redirect()->route('home')->success('Initialisierung beauftragt. Wir benachrichtigen dich per Mail wenn alles fertig ist.');
    }

    public function asCommand(Command $command): void
    {
        $this->setApi((int) $command->option('mglnr'), $command->option('password'), (int) $command->option('group'));
        self::dispatch();
    }

    private function setApi(int $mglnr, string $password, int $groupId): void
    {
        $settings = app(NamiSettings::class);
        $settings->mglnr = $mglnr;
        $settings->password = $password;
        $settings->default_group_id = $groupId;
        $settings->save();
    }
}
