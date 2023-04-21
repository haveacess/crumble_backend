<?php

namespace App\Classes\Filter;

class Filter {

    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function toArray(): array
    {
        return $this->params;
    }
}
