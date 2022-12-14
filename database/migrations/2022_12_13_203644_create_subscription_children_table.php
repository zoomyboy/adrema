<?php

use App\Payment\SubscriptionChild;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $subscriptions = DB::table('subscriptions')->get();

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->boolean('split')->default(false);
            $table->boolean('for_promise')->default(false);
        });

        Schema::create('subscription_children', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignId('parent_id')->constrained('subscriptions');
            $table->string('name');
            $table->unsignedInteger('amount');
        });

        foreach ($subscriptions as $subscription) {
            SubscriptionChild::create([
                'parent_id' => $subscription->id,
                'name' => 'name',
                'amount' => $subscription->amount,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
