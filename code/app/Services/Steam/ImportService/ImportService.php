<?php

namespace App\Services\Steam\ImportService;

use App\Entities\Cookie\CookieContainerEntity;
use App\Entities\Cookie\Formatter\FormatterUpperKey;
use App\Exceptions\DeniedOperationException;
use App\Exceptions\InvalidCookieException;
use App\Services\Steam\AuthService;
use GuzzleHttp\Cookie\SetCookie;

class ImportService {

    private string $profileAlias;
    private AuthService $authService;

    /**
     * Init import service
     *
     * @param string $profileAlias
     */
    public function __construct(string $profileAlias)
    {
        $this->profileAlias = $profileAlias;

        $this->authService = new AuthService($this->profileAlias);
    }


    /**
     * Creating new cookie container
     * or replacing existing container by new importing cookie
     *
     * @param string $cookieContent Cookie in json format
     * @param bool $allowReplace If container already exist <br>
     * you will can't do import cookie
     * @return int 0 -> Content was modified; 1 -> content was created from scratch
     * @throws InvalidCookieException Received cookie content not are valid
     * @throws DeniedOperationException Operation cannot to be continued
     */
    public function addOrReplaceCookie(string $cookieContent, bool $allowReplace): int
    {
        $container = new CookieContainerEntity($cookieContent, [new FormatterUpperKey]);

        $isEmptyContainer = $this->authService->isEmptyCookieContainer();

        if (!$isEmptyContainer && !$allowReplace) {
            throw new DeniedOperationException( 'Container with cookie already exist and rewrite is denied');
        }

        $cookieFile = $this->authService->getCookieContainer();
        $cookieFile->clear();

        foreach ($container->toArray() as $cookie) {
            $cookieFile->setCookie(new SetCookie($cookie));
        }

        return (int)$isEmptyContainer;
    }
}
