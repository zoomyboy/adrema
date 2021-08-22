<?php

use App\Activity;
use App\Subactivity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesIsLeaderColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('is_filterable')->after('name')->default(false);
        });
        Schema::table('subactivities', function (Blueprint $table) {
            $table->boolean('is_filterable')->after('slug')->default(false);
        });
        Activity::whereIn('name', ['€ Mitglied', '€ passive Mitgliedschaft', 'Schnuppermitgliedschaft', '€ LeiterIn', '€ KassiererIn', '€ KassenprüferIn'])
            ->update(['is_filterable' => true]);
        SubActivity::whereIn('name', ['Biber', 'Wölfling', 'Jungpfadfinder', 'Pfadfinder', 'Rover', 'Vorstand', 'Sonstige'])
            ->update(['is_filterable' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
}
