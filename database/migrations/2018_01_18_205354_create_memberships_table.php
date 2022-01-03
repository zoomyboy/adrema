<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->integer('activity_id');
            $table->integer('group_id')->nullable();
            $table->integer('member_id');
            $table->integer('nami_id')->nullable();
            $table->datetime('from');
            $table->timestamps();
            $table->unique(['activity_id', 'group_id', 'member_id', 'nami_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
}
