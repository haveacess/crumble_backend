<?php

namespace App\Entities;

use Carbon\Carbon;

class PriceHistoryEntity {

    public Carbon $date;
    public float $medianPrice;
    public int $volume;

    const PATTERN_STEAM_TIMESTAMP = 'M d Y h: O';

    /**
     * Getting Carbon date based on Steam timestamp
     *
     * @param string $steamTimestamp Timestamp.
     * Example: Apr 03 2023 01: +0
     * @return Carbon
     */
    public static function getDateFromSteamTimestamp(string $steamTimestamp): Carbon
    {
        return Carbon::createFromFormat(self::PATTERN_STEAM_TIMESTAMP, $steamTimestamp);
    }
}
