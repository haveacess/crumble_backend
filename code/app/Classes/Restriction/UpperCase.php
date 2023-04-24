<?php

namespace App\Classes\Restriction;

class UpperCase extends Restriction {

    public function getPreparedExpression(): string
    {
        return "BINARY :column = UPPER(:column)";
    }

    public function getPreparedName(): string
    {
        return "only_uppercase_in_:column";
    }
}
