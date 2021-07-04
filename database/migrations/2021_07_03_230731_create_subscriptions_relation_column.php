<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Payment\Subscription;
use App\Fee;
use App\Member\Member;

class CreateSubscriptionsRelationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->id()->change();
            $table->unsignedInteger('amount')->after('name');
            $table->foreignId('fee_id')->after('name')->constrained();
        });

        foreach(Fee::get() as $fee) {
            Subscription::create([
                'amount' => 1000,
                'fee_id' => $fee->id,
                'name' => $fee->name,
            ]);
        }

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('subscription_id')->after('version')->nullable()->default(1)->constrained();
        });

        Member::withoutEvents(function() {
            foreach (Member::get() as $member) {
                if (is_null($member->fee_id)) {
                    $member->update(['subscription_id' => null]);
                    continue;
                }

                $member->update(['subscription_id' => Subscription::firstWhere('fee_id', $member->fee_id)->id]);
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['fee_id']);
            $table->dropColumn('fee_id');
        });
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
}
