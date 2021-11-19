<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nami_id');
            $table->string('name');
        });
        Schema::create('course_members', function($table) {
            $table->id();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->string('organizer');
            $table->string('event_name');
            $table->unsignedInteger('nami_id');
            $table->date('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
