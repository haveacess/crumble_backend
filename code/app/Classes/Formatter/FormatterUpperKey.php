<?php

namespace App\Classes\Formatter;

use App\Entities\Cookie\CookieEntity;

class FormatterUpperKey extends Formatter {

    /**
     * Transforming each keys in cookie to uc-first
     * ex. name -> Name
     *
     * @param CookieEntity $cookie
     * @return CookieEntity
     */
    function format(CookieEntity $cookie): CookieEntity
    {
        foreach ($cookie->getKeys() as $upperCookieKey) {
            $lowerKey = strtolower($upperCookieKey);

            $cookie->setValue($upperCookieKey, $cookie->getValue($lowerKey));
            $cookie->removeValue($lowerKey);
        }

        return $cookie;
    }
}
