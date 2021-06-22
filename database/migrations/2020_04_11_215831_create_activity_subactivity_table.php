<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySubactivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_subactivity', function (Blueprint $table) {
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('subactivity_id')->constrained();
            $table->unique(['activity_id', 'subactivity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_subactivity');
    }
}
