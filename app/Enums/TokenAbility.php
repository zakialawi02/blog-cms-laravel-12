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
    const WEB_SETTING_MANAGE = 'web-setting.manage'; // Update web settings
    const NEWSLETTER_MANAGE = 'newsletter.manage'; // CRUD newsletters
    const ARTICLE_MANAGE = 'article.manage';  // Full CRUD articles
    const ARTICLE_CREATE = 'article.create';  // Create articles (writer can create)
    const ARTICLE_DELETE = 'article.delete';  // Delete own articles (writer can delete own)

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
                self::NEWSLETTER_MANAGE,
                self::ARTICLE_MANAGE,
                self::ARTICLE_CREATE,
                self::ARTICLE_DELETE,
            ],
            'writer' => [
                self::PROFILE_MANAGE,
                self::TAG_CREATE,
                self::ARTICLE_CREATE,
                self::ARTICLE_DELETE,
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
