<?php

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Subactivity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zoomyboy\LaravelNami\Nami;

class CreateMembershipsSubactivityIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->foreignId('subactivity_id')->nullable()->constrained();
            $table->unsignedBigInteger('activity_id')->change();
            $table->foreign('activity_id')->references('id')->on('activities');
        });

        Member::whereNotNull('nami_id')->get()->each(function($member): void {
            collect($member->getNamiMemberships(Nami::login(env('NAMI_ADMIN_USER'), env('NAMI_ADMIN_PW'))))->filter(
                fn ($membership): bool => dump($membership) && $membership['ends_at'] === null,
            )->each(function($membership) use ($member): void {
                if ($member->memberships()->where('nami_id', $membership['id'])->exists()) {
                    return;
                }

                $member->memberships()->create([
                    'nami_id' => $membership['id'],
                    'activity_id' => Activity::nami($membership['activity_id'])->id, 
                    'subactivity_id' => $membership['subactivity_id']
                        ? Subactivity::nami($membership['subactivity_id'])->id
                        : null, 
                    'group_id' => Group::nami($membership['group_id'])->id,
                    'created_at' => $membership['starts_at'],
                ]);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            //
        });
    }
}
