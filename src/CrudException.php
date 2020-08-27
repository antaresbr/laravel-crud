<?php

namespace Antares\Crud;

use Exception;

class CrudException extends Exception
{
    /**
     * Create a new exception for not defined data
     *
     * @param mixed $expected
     * @param mixed $got
     * @return static
     */

    /**
     * Undocumented function
     *
     * @param [type] $expected
     * @param [type] $got
     * @return void
     */
    public static function forInvalidObjectType($expected, $got)
    {
        if (!is_string($expected)) {
            $expected = is_object($expected) ? get_class($expected) : gettype($expected);
        }
        if (!is_string($got)) {
            $got = is_object($got) ? get_class($got) : gettype($got);
        }
        return new static("Invalid object type, expected '{$expected}', but got '{$got}'.");
    }

    /**
     * Create a new exception for already defined item
     *
     * @param mixed $id
     * @return static
     */
    public static function forAlreadyDefinedItem($id)
    {
        return new static("Already defined item '{$id}'.");
    }
}
