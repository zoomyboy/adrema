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
            $table->date('ps_at')->nullable();
            $table->date('more_ps_at')->nullable();
            $table->date('without_education_at')->nullable();
            $table->date('without_efz_at')->nullable();
            $table->boolean('has_svk')->default(false);
            $table->boolean('has_vk')->default(false);
            $table->boolean('multiply_pv')->default(false);
            $table->boolean('multiply_more_pv')->default(false);
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
        });
    }
};
