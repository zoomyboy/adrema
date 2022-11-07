<?php

namespace App\Letter\Actions;

use App\Letter\DocumentFactory;
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
     *
     * @var string
     */
    protected $signature = 'payment:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Bills';

    /**
     * Execute the console command.
     */
    private function handle(): int
    {
        foreach (app(DocumentFactory::class)->types as $type) {
            $letters = app(DocumentFactory::class)->repoCollection($type, 'E-Mail');

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
