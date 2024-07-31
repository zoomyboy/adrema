<?php

use App\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->after('name')->nullable();
            $table->string('lastname')->after('name')->nullable();
        });

        foreach (User::get() as $user) {
            $user->update([]);
        }

        foreach (DB::table('users')->get() as $user) {
            [$firstname, $lastname] = explode(' ', $user->name);
            DB::table('users')->where('id', $user->id)->update(['firstname' => $firstname, 'lastname' => $lastname]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable(false)->change();
            $table->string('lastname')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
        });
        foreach (DB::table('users')->get() as $user) {
            DB::table('users')->where('id', $user->id)->update(['name' => $user->firstname . ' ' . $user->lastname]);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
        });
    }
};
