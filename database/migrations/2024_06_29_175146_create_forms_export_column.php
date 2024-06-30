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
            $table->json('export')->after('config')->nullable();
        });
        DB::table('forms')->update([
            'export' => json_encode(['root' => null, 'group_by' => null, 'to_group_field' => null])
        ]);
        Schema::table('forms', function (Blueprint $table) {
            $table->json('export')->nullable(false)->change();
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
            $table->dropColumn('export');
        });
    }
};
