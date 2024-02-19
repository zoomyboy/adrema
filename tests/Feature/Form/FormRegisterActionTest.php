<?php

namespace Tests\Feature\Form;

use App\Form\Enums\NamiType;
use App\Form\Models\Form;
use App\Group;
use App\Member\Member;
use Carbon\Carbon;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

class FormRegisterActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('temp');
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

    /**
     * @dataProvider validationDataProvider
     * @param array<string, mixed> $payload
     * @param ?array<string, mixed> $messages
     */
    public function testItValidatesInput(FormtemplateFieldRequest $fieldGenerator, array $payload, ?array $messages): void
    {
        Carbon::setTestNow(Carbon::parse('2024-02-15 06:00:00'));
        $this->login()->loginNami();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([$fieldGenerator])])
            ->create();

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
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true),
            ['letter' => 'A'],
            null
        ];

        yield [
            $this->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
            ['letter' => ['Z']],
            ['letter.0' => 'Der gewählte Wert für Buchstabe ist ungültig.'],
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
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            $this->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true),
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
    }

    public function testItValidatesGroupFieldWithParentGroupField(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory()->count(3), 'children')->create();
        $foreignGroup = Group::factory()->create();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->groupField('group')->name('Gruppe')->parentGroup($group->id)->required(true)
            ])])
            ->create();

        $this->register($form, ['group' => null])
            ->assertJsonValidationErrors(['group' => 'Gruppe ist erforderlich.']);
        $this->register($form, ['group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
    }

    public function testGroupFieldCanBeNullWhenNotRequired(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->groupField('group')->parentGroup(Group::factory()->create()->id)->required(false)
            ])])
            ->create();

        $this->register($form, ['group' => null])
            ->assertOk();
    }

    public function testItValidatesGroupWithParentFieldField(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory()->has(Group::factory()->count(3), 'children'), 'children')->create();
        $foreignGroup = Group::factory()->create();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->groupField('parentgroup')->name('Übergeordnete Gruppe')->parentGroup($group->id)->required(true),
                $this->groupField('group')->name('Gruppe')->parentField('parentgroup')->required(true),
            ])])
            ->create();

        $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
        $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $group->children->first()->children->first()->id])
            ->assertOk();
    }

    // --------------------------- NamiField Tests ---------------------------
    // ***********************************************************************
    public function testItAddsMembersViaNamiFromNamiField(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505', 'firstname' => 'Abc', 'birthday' => '2023-01-05']);
        $this->createMember(['mitgliedsnr' => '5506', 'firstname' => 'Def', 'birthday' => '2023-01-06']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->textField('firstname')->required(true)->namiType(NamiType::FIRSTNAME),
                $this->dateField('birthday')->required(true)->namiType(NamiType::BIRTHDAY),
            ])])
            ->create();

        $this->register($form, ['firstname' => 'Aaaa', 'birthday' => '2021-04-05', 'members' => [['id' => '5505'], ['id' => '5506']]])
            ->assertOk();
        $this->assertCount(3, $form->participants()->get());
        $this->assertEquals('Aaaa', $form->participants->get(0)->data['firstname']);
        $this->assertEquals('Abc', $form->participants->get(1)->data['firstname']);
        $this->assertEquals('Def', $form->participants->get(2)->data['firstname']);
        $this->assertEquals('2021-04-05', $form->participants->get(0)->data['birthday']);
        $this->assertEquals('2023-01-05', $form->participants->get(1)->data['birthday']);
        $this->assertEquals('2023-01-06', $form->participants->get(2)->data['birthday']);
        $this->assertEquals([['id' => '5505'], ['id' => '5506']], $form->participants->get(0)->data['members']);
        $this->assertEquals([], $form->participants->get(1)->data['members']);
        $this->assertEquals([], $form->participants->get(2)->data['members']);
    }

    public function testItAddsOtherFieldsOfMember(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->textField('other')->required(false),
            ])])
            ->create();

        $this->register($form, ['other' => '::string::', 'members' => [['id' => '5505', 'other' => 'othervalue']]])
            ->assertOk();
        $this->assertEquals('othervalue', $form->participants->get(1)->data['other']);
    }

    public function testItValidatesMembersFields(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $this->createMember(['mitgliedsnr' => '5506']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->textField('other')->name('Andere')->required(true),
            ])])
            ->create();

        $this->register($form, ['other' => 'ooo', 'members' => [['id' => '5505', 'other' => ''], ['id' => '5506', 'other' => '']]])
            ->assertJsonValidationErrors(['members.0.other' => 'Andere für ein Mitglied ist erforderlich.'])
            ->assertJsonValidationErrors(['members.1.other' => 'Andere für ein Mitglied ist erforderlich.']);
    }

    public function testItValidatesIfMemberExists(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->textField('other')->required(true),
            ])])
            ->create();

        $this->register($form, ['other' => '::string::', 'members' => [['id' => '9999', 'other' => '::string::']]])
            ->assertJsonValidationErrors(['members.0.id' => 'Mitglied Nr 9999 ist nicht vorhanden.']);
    }

    public function testItValidatesMembersCheckboxesOptions(): void
    {
        $this->login()->loginNami();
        $this->createMember(['mitgliedsnr' => '5505']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
            ])])
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
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
            ])])
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
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->namiField('members'),
                $this->textField('other')->required(true)->forMembers(false)->options(['A', 'B']),
                $this->textField('firstname')->required(true)->namiType(NamiType::FIRSTNAME),
            ])])
            ->create();

        $this->register($form, ['firstname' => 'A', 'other' => 'B', 'members' => [['id' => '5505']]])
            ->assertOk();
        $this->assertEquals('Paula', $form->participants->get(1)->data['firstname']);
        $this->assertEquals('', $form->participants->get(1)->data['other']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createMember(array $attributes): Member
    {
        return Member::factory()->defaults()->create($attributes);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function register(Form $form, array $payload): TestResponse
    {

        return $this->postJson(route('form.register', ['form' => $form]), $payload);
    }
}
