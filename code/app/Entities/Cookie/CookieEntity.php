<?php

namespace App\Entities\Cookie;

use App\Classes\Formatter\Formatter;

class CookieEntity
{
    private array $cookie;
    private const COOKIE_KEYS = [
        'Name',
        'Value',
        'Domain',
        'Path',
        'Max-Age',
        'Expires',
        'Secure',
        'Discard',
        'HttpOnly'
    ];

    public function __construct(array $cookie)
    {
        $this->cookie = $cookie;
    }

    public function getValue(string $name) {
        return $this->cookie[$name] ?? null;
    }

    public function removeValue(string $name) {
        unset($this->cookie[$name]);
    }

    public function setValue(string $name, $value) {
        $this->cookie[$name] = $value;
    }

    public function getKeys(): array
    {
        return self::COOKIE_KEYS;
    }

    public function toArray(): array
    {
        return $this->cookie;
    }

    public function format(Formatter $formatter): CookieEntity
    {
        return $formatter->format($this);
    }
}
