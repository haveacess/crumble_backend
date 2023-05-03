<?php

namespace App\Helpers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use LogicException;

class TempTableHelper
{
    private const PATTERN_MIGRATION_FILE_NAME = 'create_%s_table';

    private Migration $migration;
    private string $table;

    /**
     * Creating instance for
     * manipulating temporary tables
     *
     * @param string $table Name of temporary table in this migration file
     * @throws FileNotFoundException Migration file is not found
     * @throws LogicException Migration file is exist, but is not a valid
     */
    public function __construct(string $table)
    {
        $migrationFileName = sprintf(self::PATTERN_MIGRATION_FILE_NAME, $table) . '.php';
        $storage = Storage::disk('local_temp_migrations');

        $fs = new Filesystem();
        $migrationInstance = $fs->requireOnce($storage->path($migrationFileName));

        if (!$migrationInstance instanceof Migration) {
           throw new LogicException('Given file is not a migration file');
        }

        $this->migration = $migrationInstance;
        $this->table = $table;
    }

    /**
     * Getting temp table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * Recreate temp table
     *
     * @return void
     */
    public function recreate()
    {
        $this->migration->down();
        $this->migration->up();
    }
}
