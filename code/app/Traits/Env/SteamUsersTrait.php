<?php

namespace App\Traits\Env;

trait SteamUsersTrait
{
    private string $usersDivider = ',';

    /**
     * Get steam user aliases
     *
     * @return array
     */
    public function getUsers():array {
        return explode($this->usersDivider, config('services.steam.users'));
    }

    /**
     * Get default Steam user alias
     *
     * @return string
     */
    public function getDefaultUser():string {
        return config('services.steam.default_user');
    }
}
