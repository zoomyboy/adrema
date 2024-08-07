<?php

namespace App\Dav;

use App\Member\Member;
use App\User;
use Sabre\CardDAV\Backend\AbstractBackend;
use Sabre\DAV\PropPatch;
use Sabre\VObject\Component\VCard;

class AddressBookBackend extends AbstractBackend
{
    /**
     * Returns the list of addressbooks for a specific user.
     *
     * Every addressbook should have the following properties:
     *   id - an arbitrary unique id
     *   uri - the 'basename' part of the url
     *   principaluri - Same as the passed parameter
     *
     * Any additional clark-notation property may be passed besides this. Some
     * common ones are :
     *   {DAV:}displayname
     *   {urn:ietf:params:xml:ns:carddav}addressbook-description
     *   {http://calendarserver.org/ns/}getctag
     *
     * @param string $principalUri
     *
     * @return array<int, array<string, string>>
     */
    public function getAddressBooksForUser($principalUri)
    {
        if (1 !== preg_match('/^principals\/(.*)$/', $principalUri, $matches)) {
            return [];
        }

        $user = User::where('email', $matches[1])->firstOrFail();

        return [
            [
                'id' => 'contacts',
                'principaluri' => $principalUri,
                'uri' => 'contacts',
                '{DAV:}displayname' => 'Kontakte',
                '{urn:ietf:params:xml:ns:carddav}addressbook-description' => 'Alle Adressen',
            ],
        ];
    }

    /**
     * Updates properties for an address book.
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
     * @param string $addressBookId
     *
     * @return void
     */
    public function updateAddressBook($addressBookId, PropPatch $propPatch)
    {
    }

    /**
     * Creates a new address book.
     *
     * This method should return the id of the new address book. The id can be
     * in any format, including ints, strings, arrays or objects.
     *
     * @param string                $principalUri
     * @param string                $url          just the 'basename' of the url
     * @param array<string, string> $properties
     *
     * @return mixed
     */
    public function createAddressBook($principalUri, $url, array $properties)
    {
    }

    /**
     * Deletes an entire addressbook and all its contents.
     *
     * @param mixed $addressBookId
     *
     * @return void
     */
    public function deleteAddressBook($addressBookId)
    {
    }

    /**
     * Returns all cards for a specific addressbook id.
     *
     * This method should return the following properties for each card:
     *   * carddata - raw vcard data
     *   * uri - Some unique url
     *   * lastmodified - A unix timestamp
     *
     * It's recommended to also return the following properties:
     *   * etag - A unique etag. This must change every time the card changes.
     *   * size - The size of the card in bytes.
     *
     * If these last two properties are provided, less time will be spent
     * calculating them. If they are specified, you can also ommit carddata.
     * This may speed up certain requests, especially with large cards.
     *
     * @param mixed $addressbookId
     *
     * @return array<int, AddressBookCard>
     */
    public function getCards($addressbookId): array
    {
        return Member::get()->map(fn ($member) => $this->cardMeta($member))->toArray();
    }

    /**
     * Returns a specfic card.
     *
     * The same set of properties must be returned as with getCards. The only
     * exception is that 'carddata' is absolutely required.
     *
     * If the card does not exist, you must return false.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     *
     * @return AddressBookCard|bool
     */
    public function getCard($addressBookId, $cardUri)
    {
        $member = Member::where('slug', $cardUri)->first();

        if (!$member) {
            return false;
        }

        return [
            ...$this->cardMeta($member),
            'carddata' => $member->toVcard()->serialize(),
        ];
    }

    /**
     * Returns a list of cards.
     *
     * This method should work identical to getCard, but instead return all the
     * cards in the list as an array.
     *
     * If the backend supports this, it may allow for some speed-ups.
     *
     * @param mixed $addressBookId
     * @param array<int, string> $uris
     *
     * @return array<int, mixed>
     */
    public function getMultipleCards($addressBookId, array $uris)
    {
        return Member::whereIn('slug', $uris)->get()->map(fn ($member) => [
            ...$this->cardMeta($member),
            'carddata' => $member->toVcard()->serialize(),
        ])->toArray();
    }

    /**
     * Creates a new card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressBooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag is for the
     * newly created resource, and must be enclosed with double quotes (that
     * is, the string itself must contain the double quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     * @param string $cardData
     *
     * @return string|null
     */
    public function createCard($addressBookId, $cardUri, $cardData)
    {
        $member = Member::fromVcard($cardUri, $cardData);
        $member->save();

        return $member->fresh()->etag;
    }

    /**
     * Updates a card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressBooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag should
     * match that of the updated resource, and must be enclosed with double
     * quotes (that is: the string itself must contain the actual quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     * @param string $cardData
     *
     * @return string|null
     */
    public function updateCard($addressBookId, $cardUri, $cardData)
    {
        return null;
    }

    /**
     * Deletes a card.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     *
     * @return bool
     */
    public function deleteCard($addressBookId, $cardUri)
    {
        return false;
    }

    /**
     * @return AddressBookCard
     */
    private function cardMeta(Member $member): array
    {
        return [
            'lastmodified' => $member->updated_at->timestamp,
            'etag' => '"' . $member->etag . '"',
            'uri' => $member->slug,
            'id' => $member->id,
            'size' => strlen($member->toVcard()->serialize()),
        ];
    }
}
