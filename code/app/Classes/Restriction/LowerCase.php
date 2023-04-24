<?php

namespace App\Classes\Restriction;

class LowerCase extends Restriction {

    public function getPreparedExpression(): string
    {
        return "BINARY :column = LOWER(:column)";
    }

    public function getPreparedName(): string
    {
        return "only_lowercase_in_:column";
    }
}
