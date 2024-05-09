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
     * @param Collection<int, Member> $members
     */
    public function handle(Collection $members): string
    {
        $csv = Writer::createFromString();

        $csv->insertOne(['Nachname', 'Vorname', 'Adresse', 'PLZ', 'Ort', 'Haupt-Telefon', 'Mobiltelefon', 'Arbeits-Telefon', 'E-Mail-Adresse']);

        foreach ($members as $member) {
            $csv->insertOne([
                $member->lastname,
                $member->firstname,
                $member->address,
                $member->zip,
                $member->location,
                $member->main_phone,
                $member->mobile_phone,
                $member->work_phone,
                $member->email,
            ]);
        }

        return $csv->toString();
    }

    public function asController(ActionRequest $request): StreamedResponse
    {
        $members = FilterScope::fromRequest($request->input('filter'))->withOptions(['hitsPerPage' => 20000])->getQuery()->get();
        $contents = $this->handle($members);

        Storage::disk('temp')->put('mitglieder.csv', $contents);

        return Storage::disk('temp')->download('mitglieder.csv');
    }
}
