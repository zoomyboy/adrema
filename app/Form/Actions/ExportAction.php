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
        return CreateExcelDocumentAction::run($form, $form->participants);
    }

    public function asController(Form $form, ActionRequest $request): StreamedResponse
    {
        $contents = $this->handle($form);

        $filename = 'tn-' . $form->slug . '.xlsx';
        Storage::disk('temp')->put($filename, $contents);

        return Storage::disk('temp')->download($filename);
    }
}
