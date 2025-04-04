<?php

use App\Member\Member;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->primary();
            $table->unsignedBigInteger('nami_id')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('blz')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('person')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
        });

        foreach (Member::get() as $member) {
            $member->bankAccount()->create([]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
