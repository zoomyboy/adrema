<?php

namespace App\Letter\Actions;

use App\Letter\BillKind;
use App\Letter\DocumentFactory;
use App\Letter\Queries\BillKindQuery;
use App\Payment\PaymentMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Mail;
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
                $letterPath = Storage::path(Tex::compile($letter)->storeIn('/tmp', 'local'));
                Mail::to($letter->getRecipient())
                    ->send(new PaymentMail($letter, $letterPath));
                app(DocumentFactory::class)->afterSingle($letter);
            }
        }

        return 0;
    }
}
