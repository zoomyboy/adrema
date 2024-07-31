<?php

namespace App;

use App\Auth\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public $guarded = [];

    /**
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getGravatarUrl(): string
    {
        return 'https://www.gravatar.com/avatar/' . hash('sha256', $this->email);
    }
}
