<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->json('description')->default($this->default())->change();
            $table->json('mail_top')->default($this->default())->nullable(false)->change();
            $table->json('mail_bottom')->default($this->default())->nullable(false)->change();
        });

        foreach (DB::table('forms')->get() as $form) {
            $mailTop = json_decode($form->mail_top, true);
            if (!$mailTop || !count($mailTop)) {
                DB::table('forms')->where('id', $form->id)->update(['mail_top' => $this->default()]);
            }
            $mailBottom = json_decode($form->mail_bottom, true);
            if (!$mailBottom || !count($mailBottom)) {
                DB::table('forms')->where('id', $form->id)->update(['mail_bottom' => $this->default()]);
            }
            $description = json_decode($form->description, true);
            if (!$description || !count($description)) {
                DB::table('forms')->where('id', $form->id)->update(['description' => $this->default()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->json('description')->default(null)->change();
            $table->json('mail_top')->default(null)->nullable(true)->change();
            $table->json('mail_bottom')->default(null)->nullable(true)->change();
        });
    }

    protected function default(): string
    {
        return json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']);
    }
};
