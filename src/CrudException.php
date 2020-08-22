<?php

namespace Antares\Crud;

use Exception;

class CrudException extends Exception
{
    /**
     * Create a new exception for not defined data
     *
     * @return static
     */
    public static function forInvalidObjectType($expected, $got)
    {
        return new static("Invalid object type, expected '{$expected}', but got '{$got}'.");
    }
}
