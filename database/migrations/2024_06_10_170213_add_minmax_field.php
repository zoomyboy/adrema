<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

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
                /** @var Collection<int, mixed> */
                $fields = $section->fields;
                $section->fields = collect($fields)->map(function ($field) {
                    if ($field->type === 'CheckboxesField') {
                        $field->min = 0;
                        $field->max = 0;
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
                /** @var Collection<int, mixed> */
                $fields = $section->fields;
                $section->fields = collect($fields)->map(function ($field) {
                    if ($field->type === 'CheckboxesField') {
                        $field->min = 0;
                        $field->max = 0;
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
