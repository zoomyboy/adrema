<?php

use App\Subactivity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesSlugColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subactivities', function (Blueprint $table) {
            $table->boolean('is_age_group')->default(false)->after('name');
            $table->string('slug')->after('name');
        });
        Subactivity::get()->each(fn ($subactivity) => $subactivity->update([]));
        Subactivity::whereIn('nami_id', [1,2,3,4,49])->get()->each(fn ($subactivity) => $subactivity->update(['is_age_group' => true]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
