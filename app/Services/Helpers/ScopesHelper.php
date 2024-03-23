<?php


namespace App\Services\Helpers;


use Illuminate\Support\Str;

class ScopesHelper
{
    /**
     * generate and get all scopes using roles
     *
     * @return array
     */
    public static function allScopes(): array
    {
        $roles = RoleHelper::allRoles();
        $scopes = [];
        foreach ($roles as $role) {
            $scopeDescription = 'Scope for' . Str::title(str_replace('_', ' ', $role['title']));
            $scopes[$role['title']] = $scopeDescription;
        }

        return $scopes;
    }

}
