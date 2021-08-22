<?php

use App\Activity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesIsMemberColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('is_member')->default(false);
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->boolean('is_try')->default(false);
        });
        Activity::firstWhere('name', 'Schnuppermitgliedschaft')->update(['is_try' => true]);
        Activity::whereIn('name', ['€ Mitglied', 'Schnuppermitgliedschaft'])->update(['is_member' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('is_member');
        });
    }
}
