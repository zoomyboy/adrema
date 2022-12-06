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
        $billKinds = DB::table('bill_kinds')->get();
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['bill_kind_id']);
        });
        Schema::drop('bill_kinds');
        Schema::table('members', function (Blueprint $table) {
            $table->renameColumn('bill_kind_id', 'bill_kind');
        });
        Schema::table('members', function (Blueprint $table) {
            $table->string('bill_kind')->change();
        });

        foreach (DB::table('members')->get() as $member) {
            if (is_null($member->bill_kind)) {
                continue;
            }
            DB::table('members')->where('id', $member->id)->update([
                'bill_kind' => $billKinds->firstWhere('id', $member->bill_kind)->name,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
