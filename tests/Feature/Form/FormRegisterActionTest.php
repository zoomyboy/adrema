<?php

namespace Tests\Feature\Form;

use App\Form\Enums\NamiField as EnumsNamiField;
use App\Form\Enums\NamiType;
use App\Form\Fields\CheckboxesField;
use App\Form\Fields\CheckboxField;
use App\Form\Fields\DateField;
use App\Form\Fields\DropdownField;
use App\Form\Fields\GroupField;
use App\Form\Fields\NamiField;
use App\Form\Fields\RadioField;
use App\Form\Fields\TextareaField;
use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Group;
use App\Member\Member;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class FormRegisterActionTest extends TestCase
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
                    FormtemplateFieldRequest::type(TextField::class)->key('vorname'),
                    FormtemplateFieldRequest::type(TextField::class)->key('nachname'),
                ]),
                FormtemplateSectionRequest::new()->fields([
                    FormtemplateFieldRequest::type(TextField::class)->key('spitzname'),
                ]),
            ])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['vorname' => 'Max', 'nachname' => 'Muster', 'spitzname' => 'Abraham'])
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
            FormtemplateFieldRequest::type(DateField::class)->name('Geburtsdatum')->maxToday(false)->key('birthday'),
            ['birthday' => 'aa'],
            ['birthday' => 'Geburtsdatum muss ein gültiges Datum sein.']
        ];

        yield [
            FormtemplateFieldRequest::type(DateField::class)->name('Geburtsdatum')->maxToday(false)->key('birthday'),
            ['birthday' => '2021-05-06'],
            null,
        ];

        yield [
            FormtemplateFieldRequest::type(DateField::class)->name('Geburtsdatum')->maxToday(true)->key('birthday'),
            ['birthday' => now()->addDay()->format('Y-m-d')],
            ['birthday' => 'Geburtsdatum muss ein Datum vor oder gleich dem ' . now()->format('d.m.Y') . ' sein.'],
        ];

        yield [
            FormtemplateFieldRequest::type(DateField::class)->name('Geburtsdatum')->maxToday(true)->key('birthday'),
            ['birthday' => now()->format('Y-m-d')],
            null,
        ];

        yield [
            FormtemplateFieldRequest::type(TextField::class)->name('Vorname der Mutter')->required(true)->key('vorname'),
            ['vorname' => ''],
            ['vorname' => 'Vorname der Mutter ist erforderlich.']
        ];

        yield [
            FormtemplateFieldRequest::type(TextField::class)->name('Vorname der Mutter')->required(true)->key('vorname'),
            ['vorname' => 5],
            ['vorname' => 'Vorname der Mutter muss ein String sein.']
        ];

        yield [
            FormtemplateFieldRequest::type(RadioField::class)->name('Ja oder Nein')->required(true)->key('yes_or_no'),
            ['yes_or_no' => null],
            ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
        ];

        yield [
            FormtemplateFieldRequest::type(RadioField::class)->name('Buchstabe')->options(['A', 'B'])->required(false)->key('letter'),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            FormtemplateFieldRequest::type(RadioField::class)->name('Buchstabe')->options(['A', 'B'])->required(true)->key('letter'),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            FormtemplateFieldRequest::type(RadioField::class)->name('Buchstabe')->options(['A', 'B'])->required(true)->key('letter'),
            ['letter' => 'A'],
            null
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxesField::class)->name('Buchstabe')->options(['A', 'B'])->key('letter'),
            ['letter' => ['Z']],
            ['letter.0' => 'Der gewählte Wert für Buchstabe ist ungültig.'],
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxesField::class)->name('Buchstabe')->options(['A', 'B'])->key('letter'),
            ['letter' => 77],
            ['letter' => 'Buchstabe muss ein Array sein.'],
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxesField::class)->name('Buchstabe')->options(['A', 'B'])->key('letter'),
            ['letter' => ['A']],
            null,
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxesField::class)->name('Buchstabe')->options(['A', 'B'])->key('letter'),
            ['letter' => []],
            null,
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxField::class)->name('Datenschutz')->required(false)->key('data'),
            ['data' => 5],
            ['data' => 'Datenschutz muss ein Wahrheitswert sein.'],
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxField::class)->name('Datenschutz')->required(false)->key('data'),
            ['data' => false],
            null
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxField::class)->name('Datenschutz')->required(true)->key('data'),
            ['data' => false],
            ['data' => 'Datenschutz muss akzeptiert werden.'],
        ];

        yield [
            FormtemplateFieldRequest::type(CheckboxField::class)->name('Datenschutz')->required(true)->key('data'),
            ['data' => true],
            null,
        ];

        yield [
            FormtemplateFieldRequest::type(DropdownField::class)->name('Ja oder Nein')->required(true)->key('yes_or_no'),
            ['yes_or_no' => null],
            ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
        ];

        yield [
            FormtemplateFieldRequest::type(DropdownField::class)->name('Buchstabe')->options(['A', 'B'])->required(false)->key('letter'),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            FormtemplateFieldRequest::type(DropdownField::class)->name('Buchstabe')->options(['A', 'B'])->required(true)->key('letter'),
            ['letter' => 'Z'],
            ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
        ];

        yield [
            FormtemplateFieldRequest::type(DropdownField::class)->name('Buchstabe')->options(['A', 'B'])->required(true)->key('letter'),
            ['letter' => 'A'],
            null
        ];

        yield [
            FormtemplateFieldRequest::type(TextareaField::class)->name('Vorname der Mutter')->required(true)->key('vorname'),
            ['vorname' => ''],
            ['vorname' => 'Vorname der Mutter ist erforderlich.']
        ];

        yield [
            FormtemplateFieldRequest::type(TextareaField::class)->name('Vorname der Mutter')->required(true)->key('vorname'),
            ['vorname' => 5],
            ['vorname' => 'Vorname der Mutter muss ein String sein.']
        ];

        yield [
            FormtemplateFieldRequest::type(TextareaField::class)->name('Vorname der Mutter')->required(true)->key('vorname'),
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
                FormtemplateFieldRequest::type(GroupField::class)->name('Gruppe')->parentGroup($group->id)->required(true)->key('group')
            ])])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['group' => null])
            ->assertJsonValidationErrors(['group' => 'Gruppe ist erforderlich.']);
        $this->postJson(route('form.register', ['form' => $form]), ['group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
    }

    public function testGroupCanBeNull(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                FormtemplateFieldRequest::type(GroupField::class)->parentGroup(Group::factory()->create()->id)->required(false)->key('group')
            ])])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['group' => null])
            ->assertOk();
    }

    public function testItValidatesGroupWithParentFieldField(): void
    {
        $this->login()->loginNami();
        $group = Group::factory()->has(Group::factory()->has(Group::factory()->count(3), 'children'), 'children')->create();
        $foreignGroup = Group::factory()->create();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                FormtemplateFieldRequest::type(GroupField::class)->name('Übergeordnete Gruppe')->parentGroup($group->id)->required(true)->key('parentgroup'),
                FormtemplateFieldRequest::type(GroupField::class)->name('Gruppe')->parentField('parentgroup')->required(true)->key('group')
            ])])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['parentgroup' => $group->children->first()->id, 'group' => $foreignGroup->id])
            ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
        $this->postJson(route('form.register', ['form' => $form]), ['parentgroup' => $group->children->first()->id, 'group' => $group->children->first()->children->first()->id])
            ->assertOk();
    }

    public function testItAddsMembersViaNami(): void
    {
        $this->login()->loginNami();
        Member::factory()->defaults()->create(['mitgliedsnr' => '5505', 'firstname' => 'Abc', 'birthday' => '2023-01-05']);
        Member::factory()->defaults()->create(['mitgliedsnr' => '5506', 'firstname' => 'Def', 'birthday' => '2023-01-06']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                FormtemplateFieldRequest::type(NamiField::class)->key('members'),
                FormtemplateFieldRequest::type(TextField::class)->key('firstname')->required(true)->namiType(NamiType::FIRSTNAME),
                FormtemplateFieldRequest::type(DateField::class)->key('birthday')->required(true)->namiType(NamiType::BIRTHDAY),
            ])])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['firstname' => 'Aaaa', 'birthday' => '2021-04-05', 'members' => [['id' => '5505'], ['id' => '5506']]])
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
        Member::factory()->defaults()->create(['mitgliedsnr' => '5505']);
        Member::factory()->defaults()->create(['mitgliedsnr' => '5506']);
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                FormtemplateFieldRequest::type(NamiField::class)->key('members'),
                FormtemplateFieldRequest::type(TextField::class)->key('other')->namiType(null),
            ])])
            ->create();

        $this->postJson(route('form.register', ['form' => $form]), ['other' => 'ooo', 'members' => [['id' => '5505', 'other' => 'O1'], ['id' => '5506', 'other' => 'O2']]])
            ->assertOk();
        $this->assertEquals('ooo', $form->participants->get(0)->data['other']);
        $this->assertEquals('O1', $form->participants->get(1)->data['other']);
        $this->assertEquals('O2', $form->participants->get(2)->data['other']);
    }
}
