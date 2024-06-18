<?php

namespace Tests\Feature\Form;

use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Group;
use App\Group\Enums\Level;
use App\Member\Member;
use Carbon\Carbon;
use Database\Factories\Member\MemberFactory;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;

class FormRegisterActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function testItSavesParticipantAsModel(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    $this->textField('vorname'),
                    $this->textField('nachname'),
                ]),
                FormtemplateSectionRequest::new()->fields([
                    $this->textField('spitzname'),
                ]),
            ])
            ->create();

        $this->register($form, ['vorname' => 'Max', 'nachname' => 'Muster', 'spitzname' => 'Abraham'])
            ->assertOk();

        $participants = $form->fresh()->participants;
        $this->assertCount(1, $participants);
        $this->assertEquals('Max', $participants->first()->data['vorname']);
        $this->assertEquals('Muster', $participants->first()->data['nachname']);
        $this->assertEquals('Abraham', $participants->first()->data['spitzname']);
    }

    public function testItSendsEmailToParticipant(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->name('Ver2')->fields([
            $this->textField('vorname')->specialType(SpecialType::FIRSTNAME),
            $this->textField('nachname')->specialType(SpecialType::LASTNAME),
            $this->textField('email')->specialType(SpecialType::EMAIL),
        ])
            ->create();

        $this->register($form, ['vorname' => 'Lala', 'nachname' => 'GG', 'email' => 'example@test.test'])
            ->assertOk();

        Mail::assertQueued(ConfirmRegistrationMail::class, fn ($message) => $message->hasTo('example@test.test', 'Lala GG') && $message->hasSubject('Deine Anmeldung zu Ver2'));
    }

    public function testItDoesntSendEmailWhenNoMailFieldGiven(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([
            $this->textField('vorname')->specialType(SpecialType::FIRSTNAME),
            $this->textField('nachname')->specialType(SpecialType::LASTNAME),
        ])
            ->create();

        $this->register($form, ['vorname' => 'Lala', 'nachname' => 'GG'])
            ->assertOk();

        Mail::assertNotQueued(ConfirmRegistrationMail::class);
    }

    /**
     * @dataProvider validationDataProvider
     * @param array<string, mixed> $payload
     * @param ?array<string, mixed> $messages
     */
    public function testItValidatesInput(FormtemplateFieldRequest $fieldGenerator, array $payload, ?array $messages): void
    {
        Carbon::setTestNow(Carbon::parse('2024-02-15 06:00:00'));
        $this->login()->loginNami();
        $form = Form::factory()->fields([$fieldGenerator])->create();

        $response = $this->postJson(route('form.register', ['form' => $form]), $payload);

        if ($messages) {
            $response->assertJsonValidationErrors($messages);
        } else {
            $response->assertOk();
        }
    }

    public function validationDataProvider(): Generator
    {
        yield [
            $this->dateField('birthday')->name('Geburtsdatum')->maxToday(false),
            ['birthday' => 'aa'],
            ['birthday' => 'Geburtsdatum muss ein gültiges Datum sein.']
        ];

        yield [
            $this->dateField('birthday')->name('Geburtsdatum')->maxToday(false),
            ['birthday' => '2021-05-06'],
            null,
        ];

        yield [
            $this->dateField('birthday')->name('Geburtsdatum')->maxToday(true),
            ['birthday' => '2024-02-16'],
            ['birthday' => 'Geburtsdatum muss ein Datum vor oder gleich dem 15.02.2024 sein.'],
        ];

        yield [
            $this->dateField('birthday')->name('Geburtsdatum')->maxToday(true),
            ['birthday' => '2024-02-15'],
            null,
        ];

        yield [
            $this->textField('vorname')->name('Vorname der Mutter')->required(true),
            ['vorname' => ''],
            ['vorname' => 'Vorname der Mutter ist erforderlich.']
        ];

        yield [
            $this->textField('vorname')->name('Vorname der Mutter')->required(true),
            ['vorname' => 5],
            ['vorname' => 'Vorname der Mutter muss ein String sein.']
        ];

        yield [
            $this->radioField('yes_or_no')->name('Ja oder Nein')->required(true),
            ['yes_or_no' => null],
            ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false)->allowcustom(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(true),
            ['letter' => 'lalalaa'],
            null,
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
            ['letter' => 'A'],
            null
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
            ['letter' => ['Z']],
            ['letter.0' => 'Der gewählte Wert für Buchstabe ist ungültig.'],
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->allowcustom(true),
            ['letter' => 'Z'],
            null,
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
            ['letter' => 77],
            ['letter' => 'Buchstabe muss ein Array sein.'],
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
            ['letter' => ['A']],
            null,
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
            ['letter' => []],
            null,
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(0)->max(2),
            ['letter' => ['A', 'B', 'C']],
            ['letter' => 'Buchstabe darf maximal 2 Elemente haben.'],
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(2)->max(0),
            ['letter' => ['A']],
            ['letter' => 'Buchstabe muss mindestens 2 Elemente haben.'],
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(1)->max(0),
            ['letter' => []],
            ['letter' => 'Buchstabe muss mindestens 1 Elemente haben.'],
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(0)->max(1),
            ['letter' => ['A', 'B']],
            ['letter' => 'Buchstabe darf maximal 1 Elemente haben.'],
        ];

        yield [
            $this->checkboxField('data')->name('Datenschutz')->required(false),
            ['data' => 5],
            ['data' => 'Datenschutz muss ein Wahrheitswert sein.'],
        ];

        yield [
            $this->checkboxField('data')->name('Datenschutz')->required(false),
            ['data' => false],
            null
        ];

        yield [
            $this->checkboxField('data')->name('Datenschutz')->required(true),
            ['data' => false],
            ['data' => 'Datenschutz muss akzeptiert werden.'],
        ];

        yield [
            $this->checkboxField('data')->name('Datenschutz')->required(true),
            ['data' => true],
            null,
        ];

        yield [
            $this->dropdownField('yes_or_no')->name('Ja oder Nein')->required(true),
            ['yes_or_no' => null],
            ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false)->allowcustom(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
            ['letter' => 'A'],
            null
        ];

        yield [
            $this->textareaField('vorname')->name('Vorname der Mutter')->required(true),
            ['vorname' => ''],
            ['vorname' => 'Vorname der Mutter ist erforderlich.']
        ];

        yield [
            $this->textareaField('vorname')->name('Vorname der Mutter')->required(true),
            ['vorname' => 5],
            ['vorname' => 'Vorname der Mutter muss ein String sein.']
        ];

        yield [
            $this->textareaField('vorname')->name('Vorname der Mutter')->required(true),
            ['vorname' => 5],
            ['vorname' => 'Vorname der Mutter muss ein String sein.']
        ];

        yield [
            $this->emailField('email')->name('Mail')->required(true),
            ['email' => 'alaaa'],
            ['email' => 'Mail muss eine gültige E-Mail-Adresse sein.']
        ];

        yield [
            $this->emailField('email')->name('Mail')->required(false),
            ['email' => 'alaaa'],
            ['email' => 'Mail muss eine gültige E-Mail-Adresse sein.']
        ];

        yield [
            $this->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
            ['numb' => 21],
            ['numb' => 'Nummer muss kleiner oder gleich 20 sein.']
        ];

        yield [
            $this->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
            ['numb' => 9],
            ['numb' => 'Nummer muss größer oder gleich 10 sein.']
        ];

        yield [
            $this->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
            ['numb' => 'asss'],
            ['numb' => 'Nummer muss eine ganze Zahl sein.']
        ];

        yield [
            $this->numberField('numb')->name('Nummer')->required(true),
            ['numb' => ''],
            ['numb' => 'Nummer ist erforderlich.']
        ];
    }

    public function testItValidatesGroupFieldWithParentGroupField(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory()->count(3), 'children')->create();
        $foreignGroup = Group::factory()->create();
        $form = Form::factory()->fields([
            $this->groupField('group')->name('Gruppe')->parentGroup($group->id)->required(true)
        ])
            ->create();

        $this->register($form, ['group' => null])
            ->assertJsonValidationErrors(['group' => 'Gruppe ist erforderlich.']);
        $this->register($form, ['group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
    }

    public function testGroupFieldCanBeUnsetWhenGiven(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory(), 'children')->create();
        $form = Form::factory()->fields([
            $this->groupField('region')->emptyOptionValue('kein Bezirk')->parentGroup($group->id)->required(false),
            $this->groupField('stamm')->name('Gruppe')->emptyOptionValue('kein Stamm')->parentField('region')->required(true)
        ])
            ->create();

        $this->register($form, ['region' => -1, 'stamm' => -1])->assertOk();
        $participants = $form->fresh()->participants;
        $this->assertEquals(-1, $participants->first()->data['region']);
        $this->assertEquals(-1, $participants->first()->data['stamm']);
    }

    public function testGroupFieldCanBeNullWhenNotRequired(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->groupField('group')->parentGroup(Group::factory()->create()->id)->required(false)
        ])
            ->create();

        $this->register($form, ['group' => null])
            ->assertOk();
    }

    public function testItValidatesGroupWithParentFieldField(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory()->has(Group::factory()->count(3), 'children'), 'children')->create();
        $foreignGroup = Group::factory()->create();
        $form = Form::factory()->fields([
            $this->groupField('parentgroup')->name('Übergeordnete Gruppe')->parentGroup($group->id)->required(true),
            $this->groupField('group')->name('Gruppe')->parentField('parentgroup')->required(true),
        ])
            ->create();

        $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
        $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $group->children->first()->children->first()->id])
            ->assertOk();
    }

    public function testItSetsMitgliedsnrForMainMember(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de', 'firstname' => 'Max', 'lastname' => 'Muster']);
        $form = Form::factory()->fields([
            $this->textField('email')->namiType(NamiType::EMAIL),
            $this->textField('firstname')->namiType(NamiType::FIRSTNAME),
            $this->textField('lastname')->namiType(NamiType::LASTNAME),
        ])
            ->create();

        $this->register($form, ['email' => 'max@muster.de', 'firstname' => 'Max', 'lastname' => 'Muster'])->assertOk();
        $this->assertEquals('9966', $form->participants->first()->mitgliedsnr);
    }

    public function testItDoesntSetMitgliedsnrWhenFieldDoesntHaveType(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de']);
        $form = Form::factory()->fields([
            $this->textField('email'),
        ])
            ->create();

        $this->register($form, ['email' => 'max@muster.de'])->assertOk();
        $this->assertNull($form->participants->first()->mitgliedsnr);
    }

    public function testItDoesntSyncMembersWhenTwoMembersMatch(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de']);
        $this->createMember(['mitgliedsnr' => '9967', 'email' => 'max@muster.de']);
        $form = Form::factory()->fields([
            $this->textField('email')->namiType(NamiType::EMAIL),
        ])
            ->create();

        $this->register($form, ['email' => 'max@muster.de'])->assertOk();
        $this->assertNull($form->participants->first()->mitgliedsnr);
        $this->assertNull($form->participants->first()->parent_id);
    }

    // --------------------------- NamiField Tests ---------------------------
    // ***********************************************************************
    public function testItAddsMitgliedsnrFromMembers(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $this->createMember(['mitgliedsnr' => '5506']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
        ])
            ->create();

        $this->register($form, ['members' => [['id' => '5505'], ['id' => '5506']]])
            ->assertOk();
        $this->assertCount(3, $form->participants()->get());
        $this->assertEquals([['id' => '5505'], ['id' => '5506']], $form->participants->get(0)->data['members']);
        $this->assertEquals([], $form->participants->get(1)->data['members']);
        $this->assertEquals([], $form->participants->get(2)->data['members']);
        $this->assertEquals($form->participants->get(0)->id, $form->participants->get(2)->parent_id);
        $this->assertEquals($form->participants->get(0)->id, $form->participants->get(1)->parent_id);
    }

    protected function memberMatchingDataProvider(): Generator
    {
        yield [
            ['email' => 'max@muster.de'],
            NamiType::EMAIL,
            'max@muster.de',
        ];

        yield [
            ['firstname' => 'Philipp'],
            NamiType::FIRSTNAME,
            'Philipp'
        ];

        yield [
            ['lastname' => 'Muster'],
            NamiType::LASTNAME,
            'Muster'
        ];

        yield [
            ['address' => 'Maxstr 5'],
            NamiType::ADDRESS,
            'Maxstr 5'
        ];

        yield [
            ['zip' => 44444],
            NamiType::ZIP,
            '44444'
        ];

        yield [
            ['location' => 'Hilden'],
            NamiType::LOCATION,
            'Hilden'
        ];

        yield [
            ['birthday' => '2023-06-06'],
            NamiType::BIRTHDAY,
            '2023-06-06'
        ];

        yield [
            [],
            NamiType::GENDER,
            'Männlich',
            fn (MemberFactory $factory) => $factory->male(),
        ];

        yield [
            ['gender_id' => null],
            NamiType::GENDER,
            '',
        ];

        yield [
            ['birthday' => '1991-10-02'],
            NamiType::AGE,
            '31'
        ];

        yield [
            ['birthday' => '1991-05-04'],
            NamiType::AGE,
            '32'
        ];

        yield [
            ['birthday' => '1991-08-15'],
            NamiType::AGEEVENT,
            '32'
        ];

        yield [
            ['mobile_phone' => '+49 7776666'],
            NamiType::MOBILEPHONE,
            '+49 7776666'
        ];
    }

    /**
     * @dataProvider memberMatchingDataProvider
     * @param array<string, string> $memberAttributes
     * @param mixed $participantValue
     */
    public function testItSynchsMemberAttributes(array $memberAttributes, NamiType $type, mixed $participantValue, ?callable $factory = null): void
    {
        Carbon::setTestNow(Carbon::parse('2023-05-04'));
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505', ...$memberAttributes], $factory);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('other')->required(true)->namiType($type),
        ])
            ->from('2023-08-15')
            ->create();

        $this->register($form, ['other' => '::other::', 'members' => [['id' => '5505']]])->assertOk();
        $this->assertEquals($participantValue, $form->participants->get(1)->data['other']);
    }

    public function testItAddsOtherFieldsOfMember(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('other')->required(false),
        ])
            ->create();

        $this->register($form, ['other' => '::string::', 'members' => [['id' => '5505', 'other' => 'othervalue']]])
            ->assertOk();
        $this->assertEquals('othervalue', $form->participants->get(1)->data['other']);
    }

    public function testItAddsMemberForNonNami(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('gender')->namiType(NamiType::GENDER)->required(false),
            $this->textField('other')->required(false),
        ])
            ->create();

        $this->register($form, ['other' => '::string::', 'members' => [['id' => null, 'gender' => 'Herr', 'other' => 'othervalue']]])
            ->assertOk();
        $this->assertEquals('othervalue', $form->participants->get(1)->data['other']);
        $this->assertEquals('Herr', $form->participants->get(1)->data['gender']);
    }

    public function testItValidatesMembersFields(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $this->createMember(['mitgliedsnr' => '5506']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('other')->name('Andere')->required(true),
        ])
            ->create();

        $this->register($form, ['other' => 'ooo', 'members' => [['id' => '5505', 'other' => ''], ['id' => '5506', 'other' => '']]])
            ->assertJsonValidationErrors(['members.0.other' => 'Andere für ein Mitglied ist erforderlich.'])
            ->assertJsonValidationErrors(['members.1.other' => 'Andere für ein Mitglied ist erforderlich.']);
    }

    public function testItValidatesIfMemberExists(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('other')->required(true),
        ])
            ->create();

        $this->register($form, ['other' => '::string::', 'members' => [['id' => '9999', 'other' => '::string::']]])
            ->assertJsonValidationErrors(['members.0.id' => 'Mitglied Nr 9999 ist nicht vorhanden.']);
    }

    public function testItValidatesMembersCheckboxesOptions(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
        ])
            ->create();

        $this->register($form, ['other' => [], 'members' => [
            ['id' => '5505', 'other' => ['A', 'missing']]
        ]])
            ->assertJsonValidationErrors(['members.0.other.1' => 'Der gewählte Wert für Andere für ein Mitglied ist ungültig.']);
    }

    public function testItValidatesMembersCheckboxesAsArray(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
        ])
            ->create();

        $this->register($form, ['other' => [], 'members' => [
            ['id' => '5505', 'other' => 'lala']
        ]])
            ->assertJsonValidationErrors(['members.0.other' => 'Andere für ein Mitglied muss ein Array sein.']);
    }

    public function testItSetsDefaultValueForFieldsThatAreNotNamiFillable(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505', 'firstname' => 'Paula']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->textField('other')->required(true)->forMembers(false)->options(['A', 'B']),
            $this->textField('firstname')->required(true)->namiType(NamiType::FIRSTNAME),
        ])
            ->create();

        $this->register($form, ['firstname' => 'A', 'other' => 'B', 'members' => [['id' => '5505']]])
            ->assertOk();
        $this->assertEquals('Paula', $form->participants->get(1)->data['firstname']);
        $this->assertEquals('', $form->participants->get(1)->data['other']);
    }

    public function testNamiFieldCanBeEmptyArray(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->namiField('members'),
        ])
            ->create();

        $this->register($form, ['members' => []])->assertOk();
        $this->assertDatabaseCount('participants', 1);
    }

    public function testNamiFieldMustBeArray(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->namiField('members'),
        ])
            ->create();

        $this->register($form, ['members' => null])->assertJsonValidationErrors(['members']);
    }

    public function testParticipantsHaveRelationToActualMember(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()->fields([
            $this->namiField('members'),
        ])
            ->create();

        $this->register($form, ['members' => [['id' => '5505']]])->assertOk();
        $this->assertEquals('5505', $form->participants->get(1)->mitgliedsnr);
    }

    public function testItSetsRegionIdAndGroupIdOfParentGroup(): void
    {
        $this->login()->loginNami();
        $bezirk = Group::factory()->level(Level::REGION)->create();
        $stamm = Group::factory()->for($bezirk, 'parent')->level(Level::GROUP)->create();
        $this->createMember(['mitgliedsnr' => '5505', 'group_id' => $stamm->id]);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->groupField('bezirk')->forMembers(false)->namiType(NamiType::REGION),
            $this->groupField('stamm')->forMembers(false)->namiType(NamiType::STAMM),
        ])
            ->create();

        $this->register($form, ['bezirk' => $bezirk->id, 'stamm' => $stamm->id, 'members' => [['id' => '5505']]])->assertOk();
        $this->assertEquals($bezirk->id, $form->participants->get(1)->data['bezirk']);
        $this->assertEquals($stamm->id, $form->participants->get(1)->data['stamm']);
    }

    public function testItSetsRegionIfMemberIsDirectRegionMember(): void
    {
        $this->login()->loginNami();
        $bezirk = Group::factory()->level(Level::REGION)->create();
        $this->createMember(['mitgliedsnr' => '5505', 'group_id' => $bezirk->id]);
        $form = Form::factory()->fields([
            $this->namiField('members'),
            $this->groupField('bezirk')->forMembers(false)->namiType(NamiType::REGION),
        ])
            ->create();

        $this->register($form, ['bezirk' => $bezirk->id, 'members' => [['id' => '5505']]])->assertOk();
        $this->assertEquals($bezirk->id, $form->participants->get(1)->data['bezirk']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createMember(array $attributes, ?callable $factoryCallback = null): Member
    {
        return call_user_func($factoryCallback ?: fn ($factory) => $factory, Member::factory()->defaults())
            ->create($attributes);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function register(Form $form, array $payload): TestResponse
    {
        return $this->postJson(route('form.register', ['form' => $form]), $payload);
    }
}
