<?php

use App\Invoice\BillKind;
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
            $table->string('usage');
            $table->string('mail_email')->nullable();
            $table->datetime('last_remembered_at')->nullable();
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

        foreach (DB::table('subscriptions')->get() as $subscription) {
            $children = DB::table('subscription_children')->where('parent_id', $subscription->id)->get();
            if ($subscription->split === 1) {
                foreach ($children as $child) {
                    $newName = 'Beitrag {year} für {name} (' . $child->name . ')';
                    DB::table('subscription_children')->where('id', $child->id)->update(['name' => $newName]);
                }
            } else {
                DB::table('subscription_children')->where('parent_id', $subscription->id)->delete();
                DB::table('subscription_children')->insert([
                    'id' => Str::uuid()->toString(),
                    'name' => 'Beitrag {year} für {name} (' . $subscription->name . ')',
                    'amount' => $children->sum('amount'),
                    'parent_id' => $subscription->id,
                ]);
            }
        }
        $paymentGroups = DB::table('payments')->where('status_id', 2)->get()->groupBy(function ($payment) {
            $member = DB::table('members')->where('id', $payment->member_id)->first();
            return $member->lastname . $member->address . $member->location . $member->zip;
        });

        foreach ($paymentGroups as $payments) {
            $member = DB::table('members')->where('id', $payments->first()->member_id)->first();
            $invoiceId = DB::table('invoices')->insertGetId([
                'to' => json_encode([
                    'name' => 'Familie ' . $member->lastname,
                    'address' => $member->address,
                    'zip' => $member->zip,
                    'location' => $member->location,
                ]),
                'greeting' => 'Liebe Familie ' . $member->lastname,
                'status' => 'Rechnung gestellt',
                'via' => BillKind::fromValue($member->bill_kind)->value,
                'usage' => 'Mitgliedsbeitrag für ' . $member->lastname,
                'mail_email' => $member->email_parents ?: $member->email,
                'last_remembered_at' => $payments->first()->last_remembered_at,
                'sent_at' => $payments->first()->last_remembered_at,
            ]);

            foreach ($payments as $payment) {
                $subscription = DB::table('subscriptions')->where('id', $payment->subscription_id)->first();
                $subscriptionChildren = DB::table('subscription_children')->where('parent_id', $subscription->id)->get();
                $paymentMember = DB::table('members')->where('id', $payment->member_id)->first();
                foreach ($subscriptionChildren as $child) {
                    DB::table('invoice_positions')->insert([
                        'invoice_id' => $invoiceId,
                        'description' => str($child->name)->replace('{name}', $paymentMember->firstname . ' ' . $paymentMember->lastname)->replace('{year}', $payment->nr),
                        'price' => $child->amount,
                        'member_id' => $member->id,
                    ]);
                }
            }
        }

        Schema::dropIfExists('payments');
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
