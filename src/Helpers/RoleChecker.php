<?php
namespace XYZSP\Helpers;


class RoleChecker
{
    public static function current_user_can_manage(): bool
    {
        $user = wp_get_current_user();
        if (!$user || empty($user->roles))
            return false;


        foreach (self::allowed_roles() as $role) {
            if (in_array($role, (array) $user->roles, true))
                return true;
        }
        return false;
    }


    public static function current_user_is_supplier(): bool
    {
        $user = wp_get_current_user();
        if (!$user || empty($user->roles))
            return false;
        return in_array('supplier', (array) $user->roles, true);
    }


    public static function allowed_roles(): array
    {
        return Constants::CAP_ALLOWED_ROLES;
    }
}