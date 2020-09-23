<?php

namespace Antares\Crud\Metadata\Filter;

use Exception;

class FilterException extends Exception
{
    /**
     * Create a new exception for property not supplied
     *
     * @return static
     */
    public static function forPropertyNotSupplied($property)
    {
        return new static("Property not supplied '{$property}'.");
    }

    /**
     * Create a new exception for mutually exclusive properties
     *
     * @return static
     */
    public static function forMutuallyExclusiveProperties($properties)
    {
        return new static("The {$properties} properties are mutually exclusive.");
    }
}
