<?php

namespace App\Course\Models;

use App\Member\Member;
use Database\Factories\Course\Models\CourseMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMember extends Model
{
    /** @use HasFactory<CourseMemberFactory> */
    use HasFactory;

    /** @var array<int, string> */
    public $guarded = [];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
