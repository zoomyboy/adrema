<?php

namespace App\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    /** @use HasFactory<\Database\Factories\\App\Member\BankAccountFactory> */
    use HasFactory;

    public $guarded = [];
}
