<?php

namespace App\Auth;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPassword extends BaseResetPassword
{
    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('Passwort zurücksetzen | Adrema'))
            ->line(Lang::get('Du erhälst diese E-Mail, weil du eine Anfrage zum zurücksetzen deines Account-Passworts gestellt hast.'))
            ->action(Lang::get('Passwort zurücksetzen'), $url)
            ->line(Lang::get('Dieser Link wird in :count Minuten ablaufen.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
            ->line(Lang::get('Wenn du die Anfrage nicht selbst gestellt hast, ist keine weitere Aktion erforderlich.'));
    }
}
