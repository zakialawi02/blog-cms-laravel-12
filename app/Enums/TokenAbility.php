<?php

namespace App\Enums;

class TokenAbility
{
    // All authenticated users
    const PROFILE_MANAGE = 'profile.manage';  // manage own profile

    // Superadmin & Admin
    const USER_MANAGE = 'user.manage';    // CRUD users
    const CATEGORY_MANAGE = 'category.manage'; // CRUD categories
    const TAG_MANAGE = 'tag.manage';          // CRUD tags
    const TAG_CREATE = 'tag.create';          // Create tags
    const MENU_MANAGE = 'menu.manage';        // CRUD menus
    const WEB_SETTING_MANAGE = 'web-setting.manage'; // Update web settings\

    /**
     * Get abilities by role.
     */
    public static function abilitiesForRole(string $role): array
    {
        return match ($role) {
            'superadmin', 'admin' => [
                self::USER_MANAGE,
                self::PROFILE_MANAGE,
                self::CATEGORY_MANAGE,
                self::TAG_MANAGE,
                self::TAG_CREATE,
                self::MENU_MANAGE,
                self::WEB_SETTING_MANAGE,
            ],
            'writer' => [
                self::PROFILE_MANAGE,
                self::TAG_CREATE,
            ],
            'user' => [
                self::PROFILE_MANAGE,
            ],
            default => [
                self::PROFILE_MANAGE,
            ],
        };
    }
}
