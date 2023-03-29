<?php

namespace App\Entities;

use App\Models\UsersModel;

class UserBaseInfoEntity {

    public string $id;
    public int $steamId64;
    public string $login;
    public string $countryCode;

    /**
     * Get entity as model
     *
     * @return UsersModel
     */
    public function toModel(): UsersModel {
        return new UsersModel([
            'id' => $this->id,
            'id_steam64' => $this->steamId64,
            'login' => $this->login,
            'country_code' => $this->countryCode
        ]);
    }
}
