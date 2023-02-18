<?php

namespace App\Letter\Actions;

use App\Letter\BillKind;
use App\Letter\DocumentFactory;
use App\Letter\Queries\BillKindQuery;
use App\Payment\PaymentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\Tex;

class LetterSendAction
{
    use AsAction;

    /**
     * The name and signature of the console command.
     */
    public string $commandSignature = 'letter:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Bills';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (app(DocumentFactory::class)->getTypes() as $type) {
            $letters = app(DocumentFactory::class)->letterCollection($type, new BillKindQuery(BillKind::EMAIL));

            foreach ($letters as $letter) {
                $letterPath = Storage::disk('temp')->path(Tex::compile($letter)->storeIn('', 'temp'));
                Mail::to($letter->getRecipient())
                    ->send(new PaymentMail($letter, $letterPath));
                app(DocumentFactory::class)->afterSingle($letter);
            }
        }

        return 0;
    }
}
