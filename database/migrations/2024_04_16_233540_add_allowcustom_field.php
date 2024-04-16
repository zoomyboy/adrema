<?php

use App\Form\Models\Form;
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
        foreach (DB::table('forms')->get() as $event) {
            $config = json_decode($event->config);
            $config->sections = array_map(function ($section) {
                $section->fields = collect($section->fields)->map(function ($field) {
                    if ($field->type === 'DropdownField' || $field->type === 'RadioField') {
                        $field->allowcustom = false;
                    }
                    return $field;
                })->all();

                return $section;
            }, $config->sections);
            DB::table('forms')->where('id', $event->id)->update(['config' => json_encode($config)]);
        }

        foreach (DB::table('formtemplates')->get() as $event) {
            $config = json_decode($event->config);
            $config->sections = array_map(function ($section) {
                $section->fields = collect($section->fields)->map(function ($field) {
                    if ($field->type === 'DropdownField' || $field->type === 'RadioField') {
                        $field->allowcustom = false;
                    }
                    return $field;
                })->all();

                return $section;
            }, $config->sections);
            DB::table('formtemplates')->where('id', $event->id)->update(['config' => json_encode($config)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
