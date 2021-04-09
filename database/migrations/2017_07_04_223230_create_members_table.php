<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('nickname')->nullable();
            $table->integer('gender_id')->unsigned()->nullable();
            $table->integer('country_id')->unsigned();
            $table->string('other_country')->nullable();
            $table->integer('confession_id')->unsigned()->nullable();
            $table->date('birthday');
            $table->date('joined_at');
            $table->boolean('sendnewspaper');
            $table->string('address');
            $table->string('further_address')->nullable();
            $table->string('zip');
            $table->string('location');
            $table->string('region_id')->nullable();
            $table->string('main_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('email_parents')->nullable();
            $table->integer('nami_id')->nullable();
            $table->integer('nationality_id')->unsigned();
            $table->integer('subscription_id')->nullable();
            
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
        Schema::dropIfExists('members');
    }
}
