<?php
namespace Papion\MembersClient\Interfaces;

interface iAccessTokenObject
{
    /**
     * Token Identifier
     *
     * @return string
     */
    function __toString();

    /**
     * Unique Token Identifier
     *
     * @return string|int
     */
    function getIdentifier();

    /**
     * Client Identifier That Token Issued To
     *
     * @return string|int
     */
    function getClientIdentifier();

    /**
     * Get the token's expiry date time
     *
     * @return \DateTime
     */
    function getDateTimeExpiration();

    /**
     * Return an array of scopes associated with the token
     *
     * @return string[]
     */
    function getScopes();

    /**
     * Resource Owner Of Token
     *
     * @return string|int|null
     */
    function getOwnerIdentifier();

    /**
     * Is Token Issued To Resource Owner?
     *
     * @return boolean
     */
    function isIssuedToResourceOwner();
}