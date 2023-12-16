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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->json('to');
            $table->string('greeting');
            $table->string('status');
            $table->date('sent_at')->nullable();
            $table->string('via');
            $table->timestamps();
        });

        Schema::create('invoice_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id');
            $table->string('description');
            $table->foreignId('member_id');
            $table->unsignedBigInteger('price');
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
        Schema::dropIfExists('invoice_positions');
        Schema::dropIfExists('invoices');
    }
};
