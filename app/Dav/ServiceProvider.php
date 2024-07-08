<?php

namespace App\Dav;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelSabre\Http\Auth\AuthBackend;
use LaravelSabre\LaravelSabre;
use Sabre\CardDAV\AddressBookRoot;
use Sabre\CardDAV\Plugin as CardDAVPlugin;
use Sabre\DAV\Auth\Plugin as AuthPlugin;
use Sabre\DAV\Browser\Plugin as BrowserPlugin;
use Sabre\DAV\ServerPlugin;
use Sabre\DAVACL\AbstractPrincipalCollection;
use Sabre\DAVACL\Plugin as AclPlugin;
use Sabre\DAVACL\PrincipalCollection;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LaravelSabre::nodes(function () {
            return $this->nodes();
        });
        LaravelSabre::plugins(fn () => $this->plugins());
        LaravelSabre::auth(function () {
            auth()->onceBasic();

            return true;
        });
    }

    /**
     * List of nodes for DAV Collection.
     *
     * @return array<int, AbstractPrincipalCollection>
     */
    private function nodes(): array
    {
        $principalBackend = new Principal();
        $addressBookBackend = new AddressBookBackend();

        // Directory tree
        return [
            new PrincipalCollection($principalBackend),
            new AddressBookRoot($principalBackend, $addressBookBackend),
        ];
    }

    /**
     * @return array<int, ServerPlugin>
     */
    private function plugins(): array
    {
        $authBackend = new AuthBackend();

        return [
            new BrowserPlugin(),
            new AuthPlugin($authBackend),
            new CardDAVPlugin(),
            new AclPlugin(),
        ];
    }
}
