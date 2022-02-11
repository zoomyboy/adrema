<?php

use App\Fee;
use App\Member\Member;
use App\Payment\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('subscription_id')->after('version')->nullable()->default(1)->constrained();
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
