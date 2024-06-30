<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Data\FileshareResourceData;
use App\Fileshare\Models\Fileshare;
use App\Form\Actions\ExportSyncAction;
use App\Form\Data\ExportData;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Group;
use Tests\FileshareTestCase;
use Tests\Lib\CreatesFormFields;

class ExportSyncActionTest extends FileshareTestCase
{

    use CreatesFormFields;

    public function testItDoesntUploadFileWhenNoExportGiven(): void
    {
        $form = Form::factory()->fields([
            $this->textField('vorname'),
            $this->textField('nachname'),
        ])->create();

        ExportSyncAction::run($form);
        $this->assertTrue(true);
    }

    public function testItUploadsRootFile(): void
    {
        $this->withoutExceptionHandling()->withOwncloudUser('badenpowell', 'secret')->withDirs('badenpowell', ['/abc']);
        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();
        $form = Form::factory()->name('Formular')->fields([
            $this->textField('vorname'),
            $this->textField('nachname'),
        ])->export(ExportData::from(['root' => FileshareResourceData::from(['connection_id' => $connection->id, 'resource' => '/abc'])]))->create();
        Participant::factory()->for($form)->data(['firstname' => 'AAA', 'lastname' => 'BBB'])->create();

        ExportSyncAction::run($form);

        $this->assertEquals(['abc/Anmeldungen Formular.xlsx'], $connection->type->getFilesystem()->files('/abc'));
        $this->assertTrue(true);
    }

    public function testItUploadsGroupFile(): void
    {
        $this->withoutExceptionHandling()->withOwncloudUser('badenpowell', 'secret')->withDirs('badenpowell', ['/abc', '/stamm']);
        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();
        $group = Group::factory()->create(['fileshare' => FileshareResourceData::from(['connection_id' => $connection->id, 'resource' => '/stamm'])]);
        $form = Form::factory()->name('Formular')->fields([
            $this->textField('vorname')->name('Vorname'),
            $this->textField('nachname')->name('Nachname'),
            $this->groupField('stamm')->name('Stamm'),
        ])->export(ExportData::from(['to_group_field' => 'stamm', 'group_by' => 'vorname', 'root' => FileshareResourceData::from(['connection_id' => $connection->id, 'resource' => '/abc'])]))->create();
        Participant::factory()->for($form)->data(['vorname' => 'AAA', 'nachname' => 'BBB', 'stamm' => $group->id])->create();
        Participant::factory()->for($form)->data(['vorname' => 'CCC', 'nachname' => 'DDD', 'stamm' => null])->create();

        ExportSyncAction::run($form);

        $this->assertEquals(['stamm/Anmeldungen Formular.xlsx'], $connection->type->getFilesystem()->files('/stamm'));
        $this->assertTrue(true);
    }
}
