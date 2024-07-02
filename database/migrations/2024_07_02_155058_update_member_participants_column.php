<?php

use App\Form\Models\Participant;
use App\Member\Member;
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
        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('mitgliedsnr', 'member_id')->nullable();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable(true)->change();
        });

        foreach (Participant::whereNotNull('member_id')->get() as $p) {
            $p->update(['member_id' => Member::firstWhere('mitgliedsnr', $p->member_id)?->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('member_id')->nullable(true)->change();
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('member_id', 'mitgliedsnr');
        });
    }
};
