<?php

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_children');
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('amount');
            $table->dropColumn('split');
            $table->dropColumn('for_promise');
        });
    }
};
