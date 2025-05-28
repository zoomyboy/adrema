<?php

namespace App\Member;

use App\Confession;
use App\Country;
use App\Course\Models\CourseMember;
use App\Gender;
use App\Group;
use App\Invoice\BillKind;
use App\Invoice\Models\InvoicePosition;
use App\Nami\HasNamiField;
use App\Nationality;
use App\Payment\Subscription;
use App\Pdf\Sender;
use App\Region;
use App\Setting\NamiSettings;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\Reader;
use Spatie\LaravelData\Lazy;
use Zoomyboy\Osm\Address;
use Zoomyboy\Osm\Coordinate;
use Zoomyboy\Osm\Geolocatable;
use Zoomyboy\Osm\HasGeolocation;
use Zoomyboy\Phone\HasPhoneNumbers;
use App\Prevention\Enums\Prevention;
use Database\Factories\Member\MemberFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $subscription_name
 * @property int    $pending_payment
 */
class Member extends Model implements Geolocatable
{
    use Notifiable;
    use HasNamiField;
    /** @use HasFactory<MemberFactory> */
    use HasFactory;
    use Sluggable;
    use Searchable;
    use HasPhoneNumbers;
    use HasGeolocation;

    /**
     * @var array<string, string>
     */
    public $guarded = [];

    /**
     * @var array<int, string>
     */
    public static array $namiFields = ['firstname', 'lastname', 'joined_at', 'birthday', 'send_newspaper', 'address', 'zip', 'location', 'nickname', 'other_country', 'further_address', 'main_phone', 'mobile_phone', 'work_phone', 'fax', 'email', 'email_parents', 'gender_id', 'confession_id', 'region_id', 'country_id', 'fee_id', 'nationality_id', 'slug', 'subscription_id', 'keepdata'];

    /**
     * @var array<string, string>
     */
    public $casts = [
        'pending_payment' => 'integer',
        'send_newspaper' => 'boolean',
        'gender_id' => 'integer',
        'way_id' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'confession_id' => 'integer',
        'nami_id' => 'integer',
        'has_svk' => 'boolean',
        'has_vk' => 'boolean',
        'multiply_pv' => 'boolean',
        'multiply_more_pv' => 'boolean',
        'is_leader' => 'boolean',
        'keepdata' => 'boolean',
        'bill_kind' => BillKind::class,
        'mitgliedsnr' => 'integer',

        'try_created_at' => 'datetime',
        'recertified_at' => 'datetime',
        'joined_at' => 'datetime',
        'birthday' => 'datetime',
        'efz' => 'datetime',
        'ps_at' => 'datetime',
        'more_ps_at' => 'datetime',
        'without_education_at' => 'datetime',
        'without_efz_at' => 'datetime',
    ];

    /**
     * @return array<int, string>
     */
    public function phoneNumbers(): array
    {
        return ['main_phone', 'mobile_phone', 'work_phone', 'children_phone', 'fax'];
    }

    /**
     * @return SluggableConfig
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => ['firstname', 'lastname']],
        ];
    }

    // ---------------------------------- Actions ----------------------------------
    public function syncVersion(): void
    {
        $version = app(NamiSettings::class)->login()->member($this->group->nami_id, $this->nami_id)->version;

        $this->update(['version' => $version]);
    }

    // ----------------------------------- Getters -----------------------------------
    public function getFullnameAttribute(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getPreferredPhoneAttribute(): ?string
    {
        if ($this->mobile_phone) {
            return $this->mobile_phone;
        }

        if ($this->main_phone) {
            return $this->main_phone;
        }

        return null;
    }

    public function getPreferredEmailAttribute(): ?string
    {
        if ($this->email) {
            return $this->email;
        }

        if ($this->email_parents) {
            return $this->email_parents;
        }

        return null;
    }

    public function getEtagAttribute(): string
    {
        return $this->updated_at->timestamp . '_' . $this->version;
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address && $this->zip && $this->location
            ? $this->address . ', ' . $this->zip . ' ' . $this->location
            : '';
    }

    public function getEfzLink(): ?string
    {
        return $this->address && $this->zip && $this->location && $this->birthday
            ? route('efz', ['member' => $this])
            : null;
    }

    public function getNamiFeeId(): ?int
    {
        if (!$this->subscription) {
            return null;
        }

        return $this->subscription->fee->nami_id;
    }

    public function isLeader(): bool
    {
        return $this->leaderMemberships->count() > 0;
    }

    public function getAge(): ?int
    {
        return $this->birthday ? intval($this->birthday->diffInYears(now())) : null;
    }

    protected function getAusstand(): int
    {
        return (int) $this->invoicePositions()->whereHas('invoice', fn($query) => $query->whereNeedsPayment())->sum('price');
    }

    // ---------------------------------- Relations ----------------------------------
    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo<Gender, $this>
     */
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->withDefault([
            'name' => '-- kein --',
            'nami_id' => null,
        ]);
    }

    /**
     * @return HasMany<InvoicePosition, $this>
     */
    public function invoicePositions(): HasMany
    {
        return $this->hasMany(InvoicePosition::class);
    }

    /**
     * @return BelongsTo<Confession, $this>
     */
    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class);
    }

    /**
     * @return BelongsTo<Nationality, $this>
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return HasMany<CourseMember, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(CourseMember::class);
    }

    /**
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * @return HasMany<Membership, $this>
     */
    public function leaderMemberships(): HasMany
    {
        return $this->ageGroupMemberships()->isLeader()->active();
    }

    /**
     * @return HasMany<Membership, $this>
     */
    public function ageGroupMemberships(): HasMany
    {
        return $this->memberships()->isAgeGroup()->active();
    }

    /**
     * @return HasOne<BankAccount, $this>
     */
    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    public static function booted()
    {
        static::created(function (self $model): void {
            $model->bankAccount()->create([]);
        });

        static::deleting(function (self $model): void {
            $model->memberships->each->delete();
            $model->courses->each->delete();
            $model->invoicePositions->each(function ($position) {
                $position->delete();
            });
            $model->bankAccount()->delete();
        });
    }

    // ---------------------------------- Scopes -----------------------------------
    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('lastname, firstname');
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWithPendingPayment(Builder $query): Builder
    {
        return $query->addSelect([
            'pending_payment' => InvoicePosition::selectRaw('SUM(price)')
                ->whereColumn('invoice_positions.member_id', 'members.id')
                ->whereHas('invoice', fn($query) => $query->whereNeedsPayment()),
        ]);
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereHasPendingPayment(Builder $query): Builder
    {
        return $query->whereHas('invoicePositions', fn($q) => $q->whereHas('invoice', fn($q) => $q->whereNeedsPayment()));
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopePayable(Builder $query): Builder
    {
        return $query->where('bill_kind', '!=', null)->where('subscription_id', '!=', null);
    }

    /**
     * @return array<int, Prevention>
     */
    public function preventions(?Carbon $date = null): array
    {
        $date = $date ?: now();

        /** @var array<int, Prevention> */
        $preventions = [];

        if ($this->efz === null || $this->efz->diffInYears($date) >= 5) {
            $preventions[] = Prevention::EFZ;
        }

        if (!$this->has_vk) {
            $preventions[] = Prevention::VK;
        }

        if ($this->more_ps_at === null) {
            if ($this->ps_at === null) {
                $preventions[] = Prevention::PS;
            } else if ($this->ps_at->diffInYears($date) >= 5) {
                $preventions[] = Prevention::MOREPS;
            }
        } else {
            if ($this->more_ps_at === null || $this->more_ps_at->diffInYears($date) >= 5) {
                $preventions[] = Prevention::MOREPS;
            }
        }

        return $preventions;
    }


    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->selectRaw('SUM(id)');
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereCurrentGroup(Builder $query): Builder
    {
        $group = app(NamiSettings::class)->localGroup();

        if (!$group) {
            return $query;
        }

        return $query->where('group_id', $group->id);
    }

    public static function fromVcard(string $url, string $data): self
    {
        $settings = app(NamiSettings::class);
        $card = Reader::read($data);
        [$lastname, $firstname] = $card->N->getParts();
        [$deprecated1, $deprecated2, $address, $location, $region, $zip, $country] = $card->ADR->getParts();

        return new self([
            'joined_at' => now(),
            'send_newspaper' => false,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'birthday' => Carbon::createFromFormat('Ymd', $card->BDAY->getValue()),
            'slug' => pathinfo($url, PATHINFO_FILENAME),
            'address' => $address,
            'zip' => $zip,
            'location' => $location,
            'group_id' => $settings->default_group_id,
            'nationality_id' => Nationality::firstWhere('name', 'deutsch')->id,
            'subscription_id' => Subscription::firstWhere('name', 'Voll')->id,
        ]);
    }

    public function toVcard(): Vcard
    {
        $card = new VCard([
            'VERSION' => '3.0',
            'FN' => $this->fullname,
            'N' => [$this->lastname, $this->firstname, '', '', ''],
            'CATEGORIES' => 'Scoutrobot',
            'UID' => $this->slug,
        ]);

        if ($this->birthday) {
            $card->add('BDAY', $this->birthday->format('Ymd'));
        }

        if ($this->main_phone) {
            $card->add('TEL', $this->main_phone, ['type' => 'voice']);
        }

        if ($this->mobile_phone) {
            $card->add('TEL', $this->mobile_phone, ['type' => 'work']);
        }

        if ($this->children_phone) {
            $card->add('TEL', $this->children_phone, ['type' => 'cell']);
        }

        if ($this->email) {
            $card->add('EMAIL', $this->email, ['type' => 'internet']);
        }

        if ($this->email_parents) {
            $card->add('EMAIL', $this->email_parents, ['type' => 'aol']);
        }

        $card->add('ADR', [
            '',
            '',
            $this->address ?: '',
            $this->location ?: '',
            $this->region?->name ?: '',
            $this->zip ?: '',
            $this->country?->name ?: '',
        ]);

        return $card;
    }

    public function toSender(): Sender
    {
        return Sender::from([
            'name' => $this->fullname,
            'address' => $this->address,
            'zipLocation' => $this->zip . ' ' . $this->location,
            'mglnr' => Lazy::create(fn() => 'Mglnr.: ' . $this->nami_id),
        ]);
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    public static function forSelect(): array
    {
        return static::select(['id', 'firstname', 'lastname'])->get()->map(fn($member) => ['id' => $member->id, 'name' => $member->fullname])->toArray();
    }

    // -------------------------------- Geolocation --------------------------------
    // *****************************************************************************
    public function fillCoordinate(Coordinate $coordinate): void
    {
        $this->updateQuietly(['lat' => $coordinate->lat, 'lon' => $coordinate->lon]);
    }

    public function getAddressForGeolocation(): ?Address
    {
        return new Address($this->address, $this->zip, $this->location);
    }

    public function destroyCoordinate(): void
    {
        $this->updateQuietly([
            'lat' => null,
            'lon' => null,
        ]);
    }

    public function needsGeolocationUpdate(): bool
    {
        return $this->getOriginal('address') !== $this->address
            || $this->getOriginal('zip') !== $this->zip
            || $this->getOriginal('location') !== $this->location;
    }

    // --------------------------------- Searching ---------------------------------
    // *****************************************************************************

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'address' => $this->fullAddress,
            'fullname' => $this->fullname,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'ausstand' => $this->getAusstand(),
            'bill_kind' => $this->bill_kind?->value,
            'group_id' => $this->group->id,
            'group_name' => $this->group->inner_name ?: $this->group->name,
            'has_vk' => $this->has_vk,
            'has_svk' => $this->has_svk,
            'links' => [
                'show' => route('member.show', ['member' => $this], false),
                'edit' => route('member.edit', ['member' => $this], false),
            ],
            'age_group_icon' => $this->ageGroupMemberships->first()?->subactivity->slug,
            'is_leader' => $this->leaderMemberships()->count() > 0,
            'memberships' => $this->memberships()->active()->get()
                ->map(fn($membership) => [...$membership->only('activity_id', 'subactivity_id'), 'both' => $membership->activity_id . '|' . $membership->subactivity_id, 'with_group' => $membership->group_id . '|' . $membership->activity_id . '|' . $membership->subactivity_id]),
        ];
    }
}
