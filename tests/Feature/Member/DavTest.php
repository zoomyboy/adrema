<?php

namespace Tests\Feature\Member;

use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DavTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanStoreAMemberFromAVcard(): void
    {
        Nationality::factory()->create(['name' => 'englisch']);
        $subscription = Subscription::factory()->create(['name' => 'Voll']);
        $nationality = Nationality::factory()->create(['name' => 'deutsch']);
        $group = Group::factory()->create();
        NamiSettings::fake(['default_group_id' => $group->id]);
        $cardUri = '97266d2e-36e7-4fb6-8b6c-bbf57a061685.vcf';
        $cardData = <<<VCARD
BEGIN:VCARD
VERSION:3.0
PRODID:-//Thunderbird.net/NONSGML Thunderbird CardBook V77.0//EN-US
UID:97266d2e-36e7-4fb6-8b6c-bbf57a061685
CATEGORIES:Scoutrobot
FN:given familya Silva
N:familya;given;;;
BDAY:20221003
ORG:Silva
EMAIL:mail@maild.ee
ITEM1.TEL:+49 176 70342420
ITEM1.X-ABLABEL:eltern
ADR:;;Itterstr 3;Solingen;NRW;42719;Germany
REV:2022-10-07T14:17:06Z
END:VCARD

VCARD;
        $member = Member::fromVcard($cardUri, $cardData);

        $member->save();

        $this->assertDatabaseHas('members', [
            'slug' => '97266d2e-36e7-4fb6-8b6c-bbf57a061685',
            'firstname' => 'given',
            'lastname' => 'familya',
            'address' => 'Itterstr 3',
            'zip' => '42719',
            'location' => 'Solingen',
            'group_id' => $group->id,
            'nationality_id' => $nationality->id,
            'subscription_id' => $subscription->id,
        ]);
    }

    public function testTheVcardHasTheMembersSlug(): void
    {
        $member = Member::factory()->defaults()->create(['firstname' => 'max', 'lastname' => 'muster']);

        $card = $member->toVcard();

        $this->assertEquals('max-muster', $card->UID->getValue());
    }

    public function testItSetsTheNames(): void
    {
        $member = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);

        $card = $member->toVcard();

        $this->assertEquals(['Muster', 'Max', '', '', ''], $card->N->getParts());
        $this->assertEquals('Max Muster', $card->FN->getValue());
    }

    public function testItSetsTheBirthday(): void
    {
        $member = Member::factory()->defaults()->create(['birthday' => '1993-05-06']);

        $card = $member->toVcard();

        $this->assertEquals('19930506', $card->BDAY->getValue());
    }

    public function testItCanSetAndUnsetMobilePhone(): void
    {
        $member = Member::factory()->defaults()->create();

        $member->update(['mobile_phone' => '+49 176 555555']);

        $this->assertTrue(count($member->toVcard()->TEL) > 0);
        foreach ($member->toVcard()->TEL as $t) {
            if (!$t['TYPE'] || 'cell' !== $t['TYPE']->getValue()) {
                continue;
            }

            $this->assertEquals('+49 176 555555', $t->getValue());

            return;
        }

        $this->assertFalse(true, 'No Phone number found in card');
    }

    public function testItUnsetsMobilePhoneNumber(): void
    {
        $member = Member::factory()->defaults()->create();

        $member->update(['mobile_phone' => '']);

        if (!is_null($member->toVcard()->TEL)) {
            foreach ($member->toVcard()->TEL as $t) {
                if ($t['TYPE'] && 'cell' === $t['TYPE']->getValue()) {
                    $this->assertFalse(true, 'Phone number found');
                    continue;
                }
            }
        }

        $this->assertTrue(true);
    }
}
