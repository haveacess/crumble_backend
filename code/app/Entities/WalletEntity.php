<?php

namespace App\Entities;

use App\Exceptions\UnauthorizedUserException;
use App\Models\UsersModel;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class WalletEntity {
    private int $balance;

    public function __construct(int $balance, $currencyId = -1)
    {
        $this->balance = $balance;
    }

    public function getBalance():int {
        return $this->balance;
    }

    public function getCurrency():string {
        return 'RUB (not using yet)'; // not using yet
    }
}
