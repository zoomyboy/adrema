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
        Schema::table('formtemplates', function (Blueprint $table) {
            $table->json('mail_top')->after('name')->default(json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']));
            $table->json('mail_bottom')->after('name')->default(json_encode(['time' => 4, 'blocks' => [], 'version' => '1.0']));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formtemplates', function (Blueprint $table) {
            $table->dropColumn('mail_top');
            $table->dropColumn('mail_bottom');
        });
    }
};
