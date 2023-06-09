<?php

use App\Letter\BillKind;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_kinds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('nickname')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('other_country')->nullable();
            $table->foreignId('confession_id')->nullable()->constrained();
            $table->date('birthday');
            $table->date('joined_at');
            $table->boolean('send_newspaper');
            $table->string('address');
            $table->string('further_address')->nullable();
            $table->string('zip');
            $table->string('location');
            $table->foreignId('group_id')->constrained();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->string('main_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('email_parents')->nullable();
            $table->integer('nami_id')->nullable();
            $table->foreignId('nationality_id')->constrained();
            $table->text('letter_address')->nullable();
            $table->foreignId('bill_kind_id')->nullable()->constrained();
            $table->foreignId('first_activity_id')->nullable()->constrained('activities');
            $table->foreignId('first_subactivity_id')->nullable()->constrained('subactivities');
            $table->unsignedInteger('version')->default(1);
            $table->datetime('confirmed_at')->nullable();
            $table->string('children_phone')->nullable();
            $table->foreignId('subscription_id')->nullable()->default(1)->constrained();

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
        Schema::dropIfExists('members');
        Schema::dropIfExists('bill_kinds');
    }
}
