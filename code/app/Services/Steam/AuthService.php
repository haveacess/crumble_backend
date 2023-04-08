<?php

namespace App\Services\Steam;

use App\Exceptions\UnauthorizedUserException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Support\Facades\Storage;

class AuthService {

    private const COOKIE_FOLDER = 'steamAuth/';
    private const BASE_URI = 'https://steamcommunity.com/';
    private const LOGIN_SECURE_PATTERN = '/^(?<steam_id64>\d*)\|\|(?<jwt_base_info>.*)\.(?<jwt_token>.*)\.(?<secret>.*)$/';

    protected Client $client;
    protected string $profileAlias;
    private FileCookieJar $cookieContainer;

    /**
     * Create auth instance for get access to authorized data
     *
     * @param string $profileAlias For identifying and understanding what is your profile
     * ex. main_profile or only_big_deals
     */
    public function __construct(string $profileAlias)
    {
        $this->profileAlias = $profileAlias;

        $this->setCookieContainer();

        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'cookies' => $this->getCookieContainer()
        ]);
    }

    /**
     * Getting profile alias
     *
     * @return string
     */
    public function getProfileAlias(): string
    {
        return $this->profileAlias;
    }

    /**
     * Checking if container empty or not
     *
     * @return bool
     */
    public function isEmptyCookieContainer(): bool
    {
        return $this->cookieContainer->count() === 0;
    }

    /**
     * Getting the path for storage your profile cookie files
     *
     * @return string
     */
    public function getCookieContainerPath(): string
    {
        return self::COOKIE_FOLDER . $this->profileAlias . '.json';
    }

    /**
     * Setting cookie container
     *
     * @return void
     */
    private function setCookieContainer() {
        $containerPath = $this->getCookieContainerPath();
        $isExistContainer = Storage::disk('local')->exists($containerPath);

        if (!$isExistContainer) {
            Storage::disk('local')->put($containerPath, '');
        }

        $this->cookieContainer = new FileCookieJar(Storage::disk('local')->path($containerPath), true);
    }

    /**
     * Getting container with cookies for manipulate this profile
     *
     * @return FileCookieJar Cookie container for use in requests
     * ex. in Guzzle
     */
    public function getCookieContainer(): FileCookieJar
    {
        return $this->cookieContainer;
    }

    /**
     * Get Steam ID 64 from cookie container
     *
     * @throws UnauthorizedUserException
     */
    protected function getSteamID64(): int
    {
        $cookie = $this->getCookieContainer()->getCookieByName('steamLoginSecure');

        if (is_null($cookie)) {
            throw new UnauthorizedUserException($this->getProfileAlias());
        }

        preg_match(self::LOGIN_SECURE_PATTERN, urldecode($cookie->getValue()), $info);;

        if (!isset($info['steam_id64'])) {
            throw new UnauthorizedUserException($this->getProfileAlias());
        }

        return (int)$info['steam_id64'];
    }
}
