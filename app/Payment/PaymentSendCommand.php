<?php

namespace App\Payment;

use App\Pdf\BillType;
use App\Pdf\PdfGenerator;
use App\Pdf\PdfRepositoryFactory;
use App\Pdf\RememberType;
use Illuminate\Console\Command;
use Mail;

class PaymentSendCommand extends Command
{
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->sendBills();
        $this->sendRemembers();

        return 0;
    }

    private function sendBills(): void
    {
        $repos = app(PdfRepositoryFactory::class)->repoCollection(BillType::class, 'E-Mail');

        foreach ($repos as $repo) {
            $generator = app(PdfGenerator::class)->setRepository($repo)->render();
            $to = (object) [
                'email' => $repo->getEmail($repo->pages->first()),
                'name' => $repo->getFamilyName($repo->pages->first()),
            ];
            Mail::to($to)->send(new PaymentMail($repo, $generator->getCompiledFilename()));
            app(PdfRepositoryFactory::class)->afterSingle($repo);
        }
    }

    private function sendRemembers(): void
    {
        $repos = app(PdfRepositoryFactory::class)->repoCollection(RememberType::class, 'E-Mail');

        foreach ($repos as $repo) {
            $generator = app(PdfGenerator::class)->setRepository($repo)->render();
            $to = (object) [
                'email' => $repo->getEmail($repo->pages->first()),
                'name' => $repo->getFamilyName($repo->pages->first()),
            ];
            Mail::to($to)->send(new PaymentMail($repo, $generator->getCompiledFilename()));
            app(PdfRepositoryFactory::class)->afterSingle($repo);
        }
    }
}
