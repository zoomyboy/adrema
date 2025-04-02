<?php

namespace App\Member;

use Database\Factories\Member\BankAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    /** @use HasFactory<BankAccountFactory> */
    use HasFactory;

    public $guarded = [];

    public $primaryKey = 'member_id';
}
