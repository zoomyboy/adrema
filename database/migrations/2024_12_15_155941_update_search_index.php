<?php

use App\Invoice\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Laravel\Scout\Console\SyncIndexSettingsCommand;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call(SyncIndexSettingsCommand::class);
        foreach (Invoice::get() as $invoice) {
            $invoice->searchable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
