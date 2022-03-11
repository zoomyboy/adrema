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
            $table->foreignId('group_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->unsignedInteger('nami_id')->nullable();
            $table->datetime('from');
            $table->timestamps();
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('subactivity_id')->nullable()->constrained();
            $table->unique(['activity_id', 'subactivity_id', 'group_id', 'member_id', 'nami_id'], 'memberships_unique');
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
