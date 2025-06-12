<?php

namespace App\Enums;

enum LayoutSection: string
{
    case HomeFeatureSection = 'home_feature_section';
    case AdsFeatured = 'ads_featured';
    case HomeSection1 = 'home_section_1';
    case HomeSection2 = 'home_section_2';
    case HomeSection3 = 'home_section_3';
    case HomeSection4 = 'home_section_4';
    case HomeSection5 = 'home_section_5';
    case HomeSidebar1 = 'home_sidebar_1';
    case HomeSidebar2 = 'home_sidebar_2';
    case HomeSidebar3 = 'home_sidebar_3';
    case HomeSidebar4 = 'home_sidebar_4';
    case AdsSidebar1 = 'ads_sidebar_1';
    case AdsSidebar2 = 'ads_sidebar_2';
    case AdsBottom1 = 'ads_bottom_1';
    case HomeBottomSection1 = 'home_bottom_section_1';
    case AdsBottom2 = 'ads_bottom_2';

    /**
     * Get all of the enum case values in an array.
     * This is useful for iterating over all defined section keys.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
