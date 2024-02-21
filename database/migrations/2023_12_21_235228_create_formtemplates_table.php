<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formtemplates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('config');
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->json('description');
            $table->text('excerpt');
            $table->json('config');
            $table->date('from');
            $table->date('to');
            $table->dateTime('registration_from')->nullable();
            $table->dateTime('registration_until')->nullable();
            $table->text('mail_top')->nullable();
            $table->text('mail_bottom')->nullable();
            $table->json('meta');
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
        Schema::dropIfExists('formtemplates');
        Schema::dropIfExists('forms');
    }
};
