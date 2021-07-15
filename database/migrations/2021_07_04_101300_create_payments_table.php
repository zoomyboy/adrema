<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Payment\Status;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function($table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_bill');
            $table->boolean('is_remember');
        });

        Status::create(['name' => 'Nicht bezahlt', 'is_bill' => true, 'is_remember' => true]);
        Status::create(['name' => 'Rechnung gestellt', 'is_bill' => false, 'is_remember' => true]);
        Status::create(['name' => 'Rechnung beglichen', 'is_bill' => false, 'is_remember' => false]);

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('nr');
            $table->foreignId('subscription_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->foreignId('member_id')->constrained();
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
    }
}