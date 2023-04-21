<?php

namespace App\Classes\Filter\Steam;

use App\Classes\Filter\Filter;

class CSGOFilter extends Filter {

    private const RETURN_AS_JSON = [
        'norender' => 1
    ];

    public static function getMarketplaceItems():Filter
    {
        return new self(array_merge([
            'query' => '',

            'search_descriptions' => 0,
            'sort_column' => 'popular',
            'sort_dir' => 'desc',
            'appid' => '730',
            'category_730_ItemSet' => [
                'any'
            ],
            'category_730_ProPlayer' => [
                'any'
            ],
            'category_730_StickerCapsule' => [
                'any'
            ],
            'category_730_TournamentTeam' => [
                'any'
            ],
            'category_730_Weapon' => [
                'any'
            ],
            'category_730_Quality' => [
                'tag_normal',
                'tag_strange',
                'tag_unusual'
            ],
            'category_730_Type' => [
                'tag_CSGO_Type_Pistol',
                'tag_CSGO_Type_SMG',
                'tag_CSGO_Type_Rifle',
                'tag_CSGO_Type_SniperRifle',
                'tag_CSGO_Type_Shotgun',
                'tag_CSGO_Type_Machinegun',
                'tag_CSGO_Type_Knife',
                'tag_Type_CustomPlayer',
                'tag_Type_Hands',
                'tag_CSGO_Tool_Name_TagTag'
            ],
        ], self::RETURN_AS_JSON));
    }
}
