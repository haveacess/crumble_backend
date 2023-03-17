<?php

namespace App\Services\Steam;

use App\Entities\UserInfo;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class AuthService {

    const COOKIE_PATH = 'steamAuth/default_user.json';
    const BASE_URI = 'https://steamcommunity.com/';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'cookies' => self::getCookieContainer()
        ]);
    }

    /**
     * Get container which store cookies.
     *
     * If container exist - retrived exist cookies
     * Otherwise - will be creating a new empty container
     *
     * @return FileCookieJar Cookie container for init guzzle http client
     */
    public static function getCookieContainer(): FileCookieJar
    {
        $isExistContainer = Storage::disk('local')->exists(self::COOKIE_PATH);

        if (!$isExistContainer) {
            Storage::disk('local')->put(self::COOKIE_PATH, '');
        }

        return new FileCookieJar(Storage::disk('local')->path(self::COOKIE_PATH), true);
    }

    /**
     * Get a base info of login user
     *
     * @return UserInfo
     */
    public function getUserInfo(): UserInfo
    {
        $html = $this->client->get('/')->getBody();

        $crawler = new Crawler($html);
        $userHtml = $crawler->filter('#application_config')->first();

        $userJson = json_decode($userHtml->attr('data-userinfo'));

        if (empty($userJson)) {
            return new UserInfo(false);
        }

        $user = new UserInfo();
        $user->isLoggedIn = $userJson->logged_in;
        $user->login = $userJson->account_name;
        $user->accountId = $userJson->accountid;
        $user->countryCode = $userJson->country_code;
        $user->steamId64 = (int)$userJson->steamid;

        return $user;
    }
}
