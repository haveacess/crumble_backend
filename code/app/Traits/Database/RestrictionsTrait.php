<?php

namespace App\Traits\Database;

use App\Classes\Restriction\Restriction;
use Illuminate\Support\Facades\DB;

trait RestrictionsTrait
{

    /**
     * Add restriction for column in your table
     *
     * @param string $table Table name
     * @param Restriction $restriction Restriction rule for specific column
     * @return void
     */
    public function addRestriction(string $table, Restriction $restriction)
    {
        $query = strtr($this->getPreparedQuery(), [
            ':table' => $table,
            ':restriction_name' => $restriction->getName(),
            ':restriction_rule' => $restriction->getExpression()
        ]);

        DB::statement($query);
    }

    private function getPreparedQuery(): string
    {
        return 'ALTER TABLE :table ADD CONSTRAINT :restriction_name CHECK(:restriction_rule)';
    }
}
