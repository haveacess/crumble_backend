<?php

namespace App\Services\Steam;

use App\Entities\UserBaseInfoEntity;
use App\Entities\WalletEntity;
use App\Exceptions\UnauthorizedUserException;
use App\Models\UsersModel;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class UserService extends AuthService {

    private const WALLET_INFO_PATTERN = '/g_rgWalletInfo.*(?<wallet_info>\{.*\})/';

    /**
     * Get a base info of login user
     *
     * @param bool $fromDatabase Use this option if you don't
     * want to request info again from Steam
     * @return UsersModel
     * @throws UnauthorizedUserException
     * @throws GuzzleException
     */
    public function getBaseInfo(bool $fromDatabase = true):UsersModel
    {
        $id = $this->getSteamID64();

        if ($fromDatabase) {
            $userInfo = UsersModel::where('id_steam64', $id)->limit(1);

            if ($userInfo->exists()) {
                return $userInfo->get()->first();
            }
        }

        $entity = $this->fetchBaseInfo();

        return UsersModel::where('id_steam64', $entity->steamId64)->limit(1)
            ->firstOr(function () use ($entity) {
                $newUser = $entity->toModel();
                $newUser->save();

                return $newUser;
        });
    }

    /**
     * Get base info of User from Steam
     *
     * @return UserBaseInfoEntity
     * @throws GuzzleException
     * @throws UnauthorizedUserException
     */
    private function fetchBaseInfo(): UserBaseInfoEntity {
        $html = $this->client->get('/')->getBody();

        $crawler = new Crawler($html);
        $userHtml = $crawler->filter('#application_config')->first();

        $userJson = json_decode($userHtml->attr('data-userinfo'));

        if (empty($userJson)) {
            throw new UnauthorizedUserException($this->profileAlias);
        }

        $entity = new UserBaseInfoEntity();
        $entity->id = $userJson->accountid;
        $entity->steamId64 = (int)$userJson->steamid;
        $entity->login = $userJson->account_name;
        $entity->countryCode = $userJson->country_code;

        return $entity;
    }

    /**
     * Get User wallet
     *
     * @return WalletEntity
     * @throws GuzzleException
     * @throws UnauthorizedUserException
     */
    public function getWallet():WalletEntity {

        $html = $this->client->get('/market')->getBody();

        preg_match(self::WALLET_INFO_PATTERN, $html, $wallet);

        if (empty($wallet['wallet_info'])) {
            throw new UnauthorizedUserException($this->getProfileAlias());
        }

        $walletInfo = json_decode($wallet['wallet_info']);

        return new WalletEntity(
            $walletInfo->wallet_balance,
            -1
        );
    }
}
