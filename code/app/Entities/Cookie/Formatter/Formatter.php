<?php

namespace App\Entities\Cookie\Formatter;

use App\Entities\Cookie\CookieEntity;

abstract class Formatter {

    /**
     * Formatting another Cookie
     * This method when using formatter
     *
     * @param CookieEntity $cookie
     * @return CookieEntity
     */
    abstract function format(CookieEntity $cookie):CookieEntity;
}
