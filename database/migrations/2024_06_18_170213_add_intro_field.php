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
                if (!property_exists($section, 'intro')) {
                    $section->intro = null;
                }
                return $section;
            }, $config->sections);
            DB::table('forms')->where('id', $event->id)->update(['config' => json_encode($config)]);
        }

        foreach (DB::table('formtemplates')->get() as $event) {
            $config = json_decode($event->config);
            $config->sections = array_map(function ($section) {
                if (!property_exists($section, 'intro')) {
                    $section->intro = null;
                }
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
