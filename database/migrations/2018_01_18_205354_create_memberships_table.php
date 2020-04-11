<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
			$table->integer('activity_id');
			$table->integer('group_id')->nullable();
			$table->integer('member_id');
			$table->integer('nami_id')->nullable();
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
