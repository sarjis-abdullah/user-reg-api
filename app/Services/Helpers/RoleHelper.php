<?php

namespace App\Services\Helpers;

use App\Models\Role;

class RoleHelper
{
    /**
     * get all roles
     *
     * @return array
     */
    public static function allRoles(): array
    {
//        $reflectionClass = new \ReflectionClass(Role::class);
//
//        $rolesWithName = array_filter($reflectionClass->getConstants(), function ($constant) {
//            return strpos($constant, 'ROLE_') === 0;
//        }, ARRAY_FILTER_USE_KEY);
//
//        return array_values($rolesWithName);

        //TODO: May increase payload time
        return Role::all()->toArray();
    }

    /**
     * get a role by id
     *
     * @param int $id
     * @return mixed|null
     */
    public static function getRoleById(int $id): mixed
    {
        $roles = self::allRoles();
        $key = array_search($id, array_column($roles, 'id'));

        return $key !== false ? $roles[$key] : null;
    }

    /**
     * get a role by title
     *
     * @param string $title
     * @return mixed
     */
    public static function getRoleByTitle(string $title): mixed
    {
        $roles = self::allRoles();
        $key = array_search($title, array_column($roles, 'title'));

        return $key !== false ? $roles[$key] : null;
    }

    /**
     * get a role's title by id
     *
     * @param int $id
     * @return mixed
     */
    public static function getRoleTitleById(int $id): mixed
    {
        $role = self::getRoleById($id);
        return $role['title'];
    }

    /**
     * get a role's id by title
     *
     * @param string $title
     * @return mixed
     */
    public static function getRoleIdByTitle(string $title): mixed
    {
        $role = self::getRoleByTitle($title);
        return $role['id'];
    }

    /**
     * get all roles by types
     *
     * @param array $types
     * @return mixed
     */
    public static function getAllRolesByTypes(array $types): mixed
    {
        $roles = self::allRoles();

        $rolesTypeTypes = [];
        foreach ($roles as $role) {
            if (in_array($role['type'], $types)) {
                $rolesTypeTypes[] = $role;
            }
        }

        return $rolesTypeTypes;
    }

    /**
     * get all roles' ids by types
     *
     * @param array $types
     * @return mixed
     */
    public static function getAllRoleIdsByTypes(array $types): mixed
    {
        $roles = self::getAllRolesByTypes($types);

        return array_column($roles, 'id');
    }


}
