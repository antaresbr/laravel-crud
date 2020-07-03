<?php

if (!function_exists('crud_path')) {
    /**
     * Return the path of the resource relative to the package
     *
     * @param string $resource
     * @return string
     */
    function crud_path($resource = null)
    {
        if (!empty($resource) and substr($resource, 0, 1) != DIRECTORY_SEPARATOR) {
            $resource = DIRECTORY_SEPARATOR . $resource;
        }
        return __DIR__ . $resource;
    }
}
