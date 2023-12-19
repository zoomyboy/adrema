<?php

use App\Payment\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function ($table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_bill');
            $table->boolean('is_remember');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('nr');
            $table->foreignId('subscription_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->datetime('last_remembered_at')->nullable();
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
        Schema::dropIfExists('payments');
        Schema::dropIfExists('statuses');
    }
}
