<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authorization\Roles\Orm;

use Opulence\Authorization\Roles\RoleMembership;

/**
 * Defines the interface for role membership repositories to implement
 */
interface IRoleMembershipRepository
{
    /**
     * Adds a role membership
     *
     * @param RoleMembership $roleMembership The role membership to add
     */
    public function add(&$roleMembership);

    /**
     * Deletes a role membership
     *
     * @param RoleMembership $roleMembership The role membership to delete
     */
    public function delete(&$roleMembership);

    /**
     * Gets all the role memberships
     *
     * @return RoleMembership[] The list of all the role memberships
     */
    public function getAll() : array;

    /**
     * Gets the role membership with the input Id
     *
     * @param int|string $id The Id of the role membership we're searching for
     * @return RoleMembership The role membership with the input Id
     */
    public function getById($id);

    /**
     * Gets the memberships with the role Id
     *
     * @param int|string $roleId The role Id whose memberships we want
     * @return RoleMembership[] Gets the memberships by role Id
     */
    public function getByRoleId($roleId) : array;

    /**
     * Gets the membership for a user with the input Id
     *
     * @param int|string $userId The user Id
     * @param int|string $roleId The role Id to search for
     * @return RoleMembership|null The role membership if one was found, otherwise false
     */
    public function getByUserAndRoleId($userId, $roleId);

    /**
     * Gets the list of role memberships for a user
     *
     * @param int|string $userId The user Id
     * @return RoleMembership[] The list of role memberships for this user
     */
    public function getByUserId($userId) : array;
}