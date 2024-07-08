<?php

namespace App\Dav;

use App\User;
use Sabre\DAV\PropPatch;
use Sabre\DAVACL\PrincipalBackend\BackendInterface as PrincipalBackendInterface;

class Principal implements PrincipalBackendInterface
{
    /**
     * Returns a list of principals based on a prefix.
     *
     * This prefix will often contain something like 'principals'. You are only
     * expected to return principals that are in this base path.
     *
     * You are expected to return at least a 'uri' for every user, you can
     * return any additional properties if you wish so. Common properties are:
     *   {DAV:}displayname
     *   {http://sabredav.org/ns}email-address - This is a custom SabreDAV
     *     field that's actually injected in a number of other properties. If
     *     you have an email address, use this property.
     *
     * @param string $prefixPath
     *
     * @return array<int, array<string, string>>
     */
    public function getPrincipalsByPrefix($prefixPath)
    {
        if ('principals' !== $prefixPath) {
            return [];
        }

        return User::get()->map(fn ($user) => $this->userToPrincipal($user))->toArray();
    }

    /**
     * Returns a specific principal, specified by it's path.
     * The returned structure should be the exact same as from
     * getPrincipalsByPrefix.
     *
     * @param string $path
     *
     * @return array<string, string>
     */
    public function getPrincipalByPath($path)
    {
        if (1 !== preg_match('/^principals\/(.*)$/', $path, $matches)) {
            return [];
        }

        $user = User::where('email', $matches[1])->firstOrFail();

        return $this->userToPrincipal($user);
    }

    /**
     * Updates one ore more webdav properties on a principal.
     *
     * The list of mutations is stored in a Sabre\DAV\PropPatch object.
     * To do the actual updates, you must tell this object which properties
     * you're going to process with the handle() method.
     *
     * Calling the handle method is like telling the PropPatch object "I
     * promise I can handle updating this property".
     *
     * Read the PropPatch documentation for more info and examples.
     *
     * @param string $path
     *
     * @return void
     */
    public function updatePrincipal($path, PropPatch $propPatch)
    {
    }

    /**
     * This method is used to search for principals matching a set of
     * properties.
     *
     * This search is specifically used by RFC3744's principal-property-search
     * REPORT.
     *
     * The actual search should be a unicode-non-case-sensitive search. The
     * keys in searchProperties are the WebDAV property names, while the values
     * are the property values to search on.
     *
     * By default, if multiple properties are submitted to this method, the
     * various properties should be combined with 'AND'. If $test is set to
     * 'anyof', it should be combined using 'OR'.
     *
     * This method should simply return an array with full principal uri's.
     *
     * If somebody attempted to search on a property the backend does not
     * support, you should simply return 0 results.
     *
     * You can also just return 0 results if you choose to not support
     * searching at all, but keep in mind that this may stop certain features
     * from working.
     *
     * @param string                $prefixPath
     * @param array<string, string> $searchProperties
     * @param string                $test
     *
     * @return array<int, string>
     */
    public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
    {
        return [];
    }

    /**
     * Finds a principal by its URI.
     *
     * This method may receive any type of uri, but mailto: addresses will be
     * the most common.
     *
     * Implementation of this API is optional. It is currently used by the
     * CalDAV system to find principals based on their email addresses. If this
     * API is not implemented, some features may not work correctly.
     *
     * This method must return a relative principal path, or null, if the
     * principal was not found or you refuse to find it.
     *
     * @param string $uri
     * @param string $principalPrefix
     *
     * @return string|null
     */
    public function findByUri($uri, $principalPrefix)
    {
    }

    /**
     * Returns the list of members for a group-principal.
     *
     * @param string $principal
     *
     * @return array<int, string>|null
     */
    public function getGroupMemberSet($principal)
    {
        return [];
    }

    /**
     * Returns the list of groups a principal is a member of.
     *
     * @param string $principal
     *
     * @return array<int, string>|null
     */
    public function getGroupMembership($principal)
    {
        if (1 !== preg_match('/^\/?principals\/(.*)$/', $principal, $matches)) {
            return null;
        }

        return ['addressbooks/' . $matches[1]];
    }

    /**
     * Updates the list of group members for a group principal.
     *
     * The principals should be passed as a list of uri's.
     *
     * @param string $principal
     * @param array<int, string> $members
     * @return void
     */
    public function setGroupMemberSet($principal, array $members)
    {
    }

    /**
     * @return array<string, string>
     */
    private function userToPrincipal(User $user): array
    {
        return [
            '{DAV:}displayname' => $user->name,
            'uri' => 'principals/' . $user->email,
            '{http://sabredav.org/ns}email-address' => $user->email,
        ];
    }
}
