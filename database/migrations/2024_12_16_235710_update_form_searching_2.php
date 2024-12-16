<?php

use App\Form\Actions\UpdateParticipantSearchIndexAction;
use App\Form\Models\Form;
use App\Lib\Sorting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Form::get() as $form) {
            UpdateParticipantSearchIndexAction::run($form);
            foreach ($form->participants as $participant) {
                $participant->searchable();
            }
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
