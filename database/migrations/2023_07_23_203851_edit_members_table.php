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
            $table->unsignedBigInteger('nationality_id')->nullable(true)->change();
            $table->date('birthday')->nullable(true)->change();
            $table->string('address')->nullable(true)->change();
            $table->string('zip')->nullable(true)->change();
            $table->string('location')->nullable(true)->change();
            $table->date('joined_at')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('nationality_id')->nullable(false)->change();
            $table->date('birthday')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('zip')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->date('joined_at')->nullable(false)->change();
        });
    }
};
