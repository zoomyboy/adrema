<?php

namespace App\Fileshare\ConnectionTypes;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

class NextcloudConnection extends OwncloudConnection
{

    public function check(): bool
    {
        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($this->user, $this->password)
                ->withHeaders(['OCS-APIRequest' => 'true'])
                ->acceptJson()
                ->get($this->baseUrl . '/ocs/v2.php/cloud/capabilities');
            return $response->ok();
        } catch (ConnectionException $e) {
            return false;
        }
    }

    public static function title(): string
    {
        return 'Nextcloud';
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
