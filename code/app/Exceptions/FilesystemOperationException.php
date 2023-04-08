<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class FilesystemOperationException extends Exception
{
    private string $entity;
    private string $operationType;
    private string $path;

    public const OPERATION_GET = 'get';
    public const OPERATION_PUT = 'put';
    public const OPERATION_APPEND = 'append';
    public const OPERATION_COPY = 'copy';
    public const OPERATION_DELETE = 'delete';
    public const OPERATION_PREPEND = 'prepend';
    public const OPERATION_MOVE = 'move';

    public const ENTITY_DIR = 'Directory';
    public const ENTITY_FILE = 'File';

    /**
     * When has some troubles with file or folder
     *
     * @param string $entity Is file or folder (self::ENTITY_*)
     * @param string $operationType What's going on with file or folder (SELF::OPERATION_*)
     * @param string $path Path to file or folder
     */
    public function __construct(string $entity, string $operationType, string $path)
    {
        $this->entity = $entity;
        $this->operationType = $operationType;
        $this->path = $path;
    }

    public function context():array {
        return [
            'entity' => $this->entity,
            'path' => $this->path
        ];
    }

    public function getReason():string {
        return "{$this->entity} cannot to {$this->operationType} in filesystem";
    }
}
