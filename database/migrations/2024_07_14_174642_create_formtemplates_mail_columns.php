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
        Schema::table('forms', function (Blueprint $table) {
            $table->json('description')->default(json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']))->change();
            $table->json('mail_top')->default(json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']))->nullable(false)->change();
            $table->json('mail_bottom')->default(json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']))->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->json('description')->default(null)->change();
            $table->json('mail_top')->default(null)->nullable(true)->change();
            $table->json('mail_bottom')->default(null)->nullable(true)->change();
        });
    }
};
