<?php

namespace App\Invoice\Models;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\InvoiceDocument;
use App\Invoice\RememberDocument;
use App\Invoice\Scopes\InvoiceFilterScope;
use App\Member\Member;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

class Invoice extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'to' => 'json',
        'status' => InvoiceStatus::class,
        'via' => BillKind::class,
    ];

    /** @var array<int, string> */
    public $dates = [
        'sent_at',
        'last_remembered_at',
    ];

    /**
     * @return HasMany<InvoicePosition>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(InvoicePosition::class);
    }

    /**
     * @param Collection<int, Member> $members
     */
    public static function createForMember(Member $member, Collection $members, int $year, Subscription $subscription = null): self
    {
        $subscription = $subscription ?: $member->subscription;
        $invoice = new self([
            'to' => [
                'name' => 'Familie ' . $member->lastname,
                'address' => $member->address,
                'zip' => $member->zip,
                'location' => $member->location,
            ],
            'greeting' => 'Liebe Familie ' . $member->lastname,
            'status' => InvoiceStatus::NEW,
            'via' => $member->bill_kind,
            'usage' => 'Mitgliedsbeitrag für ' . $member->lastname,
            'mail_email' => $member->email_parents ?: $member->email,
        ]);

        $positions = collect([]);
        foreach ($members as $member) {
            foreach ($subscription->children as $child) {
                $positions->push([
                    'description' => str($child->name)->replace('{name}', $member->firstname . ' ' . $member->lastname)->replace('{year}', (string) $year),
                    'price' => $child->amount,
                    'member_id' => $member->id,
                    'id' => null,
                ]);
            }
        }
        $invoice->setRelation('positions', $positions);

        return $invoice;
    }

    public static function booted(): void
    {
        static::deleting(function ($model) {
            $model->positions()->delete();
        });
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsPayment(Builder $query): Builder
    {
        return $query->whereIn('status', [InvoiceStatus::NEW->value, InvoiceStatus::SENT->value]);
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsBill(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::NEW);
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsRemember(Builder $query): Builder
    {
        return $query
            ->where('status', InvoiceStatus::SENT)
            ->whereNotNull('sent_at')
            ->whereNotNull('last_remembered_at')
            ->where('last_remembered_at', '<=', now()->subMonths(3));
    }

    /**
     * @param  Builder<self> $query
     * @return Builder<self>
     */
    public function scopeWithFilter(Builder $query, InvoiceFilterScope $filter): Builder
    {
        return $filter->apply($query);
    }

    public function getMailRecipient(): stdClass
    {
        return (object) [
            'email' => $this->mail_email,
            'name' => $this->to['name']
        ];
    }

    public function sent(InvoiceDocument $document): void
    {
        if (is_a($document, BillDocument::class)) {
            $this->update([
                'sent_at' => now(),
                'status' => InvoiceStatus::SENT,
                'last_remembered_at' => now(),
            ]);
        }

        if (is_a($document, RememberDocument::class)) {
            $this->update([
                'last_remembered_at' => now(),
                'status' => InvoiceStatus::SENT,
            ]);
        }
    }
}
