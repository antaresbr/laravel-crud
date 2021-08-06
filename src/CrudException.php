<?php

namespace Antares\Crud;

use Exception;

class CrudException extends Exception
{
    /**
     * Create a new exception for invalid object type
     *
     * @param mixed $expected
     * @param mixed $got
     * @return static
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

    /**
     * Create a new exception for invalid field assign
     *
     * @param mixed $assign
     * @return static
     */
    public static function forInvalidFieldAssign($assign)
    {
        if (!is_string($assign)) {
            $assign = print_r($assign, true);
        }
        return new static("Invalid field assign '{$assign}'.");
    }

    /**
     * Create a new exception for invalid detail bond
     *
     * @param mixed $bond
     * @return static
     */
    public static function forInvalidDetailBond($bond)
    {
        if (!is_string($bond)) {
            $bond = print_r($bond, true);
        }
        return new static("Invalid detail bond '{$bond}'.");
    }

    /**
     * Create a new exception for differe
     *
     * @param mixed $bond
     * @return static
     */
    public static function forDifferentArrayLengths($array1, $array2)
    {
        $data1 = print_r($array1, true);
        $data2 = print_r($array2, true);
        return new static("Different array lengths: '{$data1}' and {$data2}.");
    }
}
