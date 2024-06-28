<?php

namespace App\Fileshare\ConnectionTypes;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class OwncloudConnection extends ConnectionType
{

    public function __construct(
        public string $user,
        public string $password,
        public string $baseUrl,
    ) {
    }

    public function check(): bool
    {
        try {
            $response = Http::withoutVerifying()->withBasicAuth($this->user, $this->password)->acceptJson()->get($this->baseUrl . '/ocs/v1.php/cloud/capabilities?format=json');
            return $response->ok();
        } catch (ConnectionException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function defaults(): array
    {
        return [
            'user' => '',
            'password' => '',
            'base_url' => '',
        ];
    }

    public static function title(): string
    {
        return 'Owncloud';
    }

    /**
     * @inheritdoc
     */
    public static function fields(): array
    {
        return [
            ['label' => 'URL', 'key' => 'base_url', 'type' => 'text'],
            ['label' => 'Benutzer', 'key' => 'user', 'type' => 'text'],
            ['label' => 'Passwort', 'key' => 'password', 'type' => 'password'],
        ];
    }

    public function getFilesystem(): FilesystemAdapter
    {
        $adapter = new WebDAVAdapter(new Client([
            'baseUri' =>  $this->baseUrl . '/remote.php/dav/files/' . $this->user,
            'userName' => $this->user,
            'password' => $this->password,
        ]), '/remote.php/dav/files/' . $this->user);

        return new FilesystemAdapter(new Filesystem($adapter), $adapter);
    }
}
