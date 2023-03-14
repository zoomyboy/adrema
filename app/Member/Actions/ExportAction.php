<?php

namespace App\Member\Actions;

use App\Member\FilterScope;
use App\Member\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAction
{
    use AsAction;

    /**
     * @param Collection<int, Member>
     */
    public function handle(Collection $members): string
    {
        $csv = Writer::createFromString();

        $csv->insertOne(['Nachname', 'Vorname', 'Adresse', 'PLZ', 'Ort', 'Haupt-Telefon', 'Mobiltelefon', 'Arbeits-Telefon']);

        foreach ($members as $member) {
            $csv->insertOne([
                $member->lastname,
                $member->firstname,
                $member->address,
                $member->zip,
                $member->location,
                $member->mainPhone,
                $member->mobilePhone,
                $member->workPhone,
            ]);
        }

        return $csv->toString();
    }

    public function asController(ActionRequest $request): StreamedResponse
    {
        $filter = FilterScope::fromRequest($request->input('filter'));

        $contents = $this->handle(Member::withFilter($filter)->get());

        Storage::disk('temp')->put('mitglieder.csv', $contents);

        return Storage::disk('temp')->download('mitglieder.csv');
    }
}
