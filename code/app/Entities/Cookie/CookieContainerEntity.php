<?php

namespace App\Entities\Cookie;

use App\Entities\Cookie\Formatter\Formatter;
use App\Exceptions\InvalidCookieException;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Arr;
use RuntimeException;

class CookieContainerEntity {

    private string $jsonContainer;

    /**
     * Init cookie container
     *
     * @param string $container List of cookies as json
     *
     * Ex.
     * [
     *      {"Name":"timezoneOffset","Value":"1","Domain":"google.com","Path":"\/..} <br>
     *      {"Name":"userTime","Value":"2","Domain":"google.com","Path":"\/..} <br>
     * ]
     * @param Formatter[] $formatters List of formatters
     * @throws InvalidCookieException
     * @return CookieContainerEntity
     */
    public function __construct(string $container, array $formatters = [])
    {
        $this->jsonContainer = $container;

        if ($formatters) {
            $this->formatCookies($formatters);
        }

        $this->isValid();

        return $this;
    }

    /**
     * Validating container
     *
     * @return void All cookies in container passed validation
     * @throws InvalidCookieException
     */
    private function isValid(): void
    {
        try {
            $container = new CookieJar(true, $this->toArray());

            if ($container->count() === 0) {
                throw new InvalidCookieException('cookie container cannot be empty');
            }
        } catch (RuntimeException $exception) {
            throw new InvalidCookieException($exception->getMessage());
        }
    }

    /**
     * Converting container to array
     * @return array
     * @throws InvalidCookieException
     */
    public function toArray():array
    {
        $result = json_decode($this->jsonContainer, true);

        if (is_null($result)) {
            throw new InvalidCookieException('error when decoding cookies container. Most likely it\'s not json');
        }

        return $result;
    }

    /**
     * Converting cookie container
     * represent as Array to Json format
     *
     * @param array $container List of cookies as array
     *
     * @throws InvalidCookieException
     */
    public function toJson(array $container): string
    {
        $result = json_encode($container);

        if ($result === false) {
            throw new InvalidCookieException('error when encode cookie container');
        }

        return $result;
    }

    /**
     * Formatting each cookie in container to needed format
     *
     * @param Formatter[] $formatters
     * @throws InvalidCookieException
     */
    private function formatCookies(array $formatters)
    {
        $formattedContainer = Arr::map($this->toArray(), function ($cookie) use ($formatters) {
            $cookieEntity = new CookieEntity($cookie);

            foreach ($formatters as $formatter) {
                $cookieEntity->format($formatter);
            }

            return $cookieEntity->toArray();
        });

        $this->jsonContainer = self::toJson($formattedContainer);
    }

    public function __toString(): string
    {
        return $this->jsonContainer;
    }
}
