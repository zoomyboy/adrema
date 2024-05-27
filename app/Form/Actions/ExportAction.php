<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAction
{
    use AsAction;

    public function handle(Form $form): string
    {
        $csv = Writer::createFromString();

        $csv->insertOne($form->getFields()->names());

        foreach ($form->participants as $participant) {
            $csv->insertOne($participant->getFields()->presentValues());
        }

        return $csv->toString();
    }

    public function asController(Form $form, ActionRequest $request): StreamedResponse
    {
        $contents = $this->handle($form);

        $filename = 'tn-' . $form->slug . '.csv';
        Storage::disk('temp')->put($filename, $contents);

        return Storage::disk('temp')->download($filename);
    }
}
