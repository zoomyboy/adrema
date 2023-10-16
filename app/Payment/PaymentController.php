<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Lib\Events\ClientMessage;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function destroy(Request $request, Member $member, Payment $payment): Response
    {
        $payment->delete();

        ClientMessage::make('Zahlung gelöscht.')->shouldReload()->dispatch();

        return response('');
    }
}
