<?php

namespace App\Classes\Restriction;

abstract class Restriction {

    protected string $column;

    /**
     * Creating check for this column
     * in the table
     *
     * @param string $column Column name to be checked
     */
    public function __construct(string $column)
    {
        $this->column= $column;
    }

    /**
     * Define expression, which used in CHECK() function
     * for verifying you column. <br><br>
     * You may use aliases for receive some data - <br>
     * :column - Column name which will be checked
     * @return string
     */
    abstract protected function getPreparedExpression(): string;

    /**
     * Define name for describe you restriction. <br>
     * This name will use for name of function in database. <br>
     * And this name will show when some data failed
     * verification by your expression <br><br>
     * You may use aliases for receive some data - <br>
     * :column - Column name which will be checked
     *
     * @return string
     */
    abstract protected function getPreparedName(): string;

    /**
     * Compiled expression with merged aliases
     *
     * @return string
     */
    public function getExpression(): string
    {
        return strtr($this->getPreparedExpression(), $this->getAliases());
    }

    /**
     * Compiled name with merged aliases
     *
     * @return string
     */
    public function getName(): string
    {
        return strtr($this->getPreparedName(), $this->getAliases());
    }

    /**
     * List of aliases which may used in query
     *
     * @return array
     */
    private function getAliases(): array
    {
        return [
            ':column' => $this->column
        ];
    }

}
