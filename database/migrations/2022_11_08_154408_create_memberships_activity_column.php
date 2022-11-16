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
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['first_subactivity_id']);
            $table->dropForeign(['first_activity_id']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('first_activity_id');
            $table->dropColumn('first_subactivity_id');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')->on('members');
        });
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
