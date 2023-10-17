<?php

namespace App\Course\Models;

use App\Member\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMember extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    public $guarded = [];

    /**
     * @return BelongsTo<Course, self>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Member, self>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
