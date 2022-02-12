<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_member')->default(false);
            $table->boolean('is_try')->default(false);
            $table->unsignedInteger('nami_id');
        });
        Schema::create('subactivities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_age_group')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->unsignedInteger('nami_id')->nullable();
        });
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
        Schema::dropIfExists('activities');
    }
}
