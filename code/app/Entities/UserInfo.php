<?php

namespace App\Entities;

class UserInfo
{
    public function __construct($isLoggedIn = true)
    {
        $this->isLoggedIn = $isLoggedIn;
    }

    /**
     * When the user is logged in > true.
     * When is anonymous > false
     */
    public bool $isLoggedIn;

    /**
     * SteamID 64
     * ex. 76562000000000000
     */
    public int $steamId64;

    /**
     * ID of account
     * ex. 221111111
     */
    public int $accountId;

    /**
     * Login of account
     * ex. user_login_here
     */
    public string $login;

    /**
     * Country which account was registered
     * ex. RU
     */
    public string $countryCode;
}
