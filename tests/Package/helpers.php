<?php

if (!function_exists('ai_package_path')) {
    /**
     * Return the path of the resource relative to the package
     *
     * @param string $resource
     * @return string
     */
    function ai_package_path($resource = null)
    {
        if (!empty($resource) and substr($resource, 0, 1) != DIRECTORY_SEPARATOR) {
            $resource = DIRECTORY_SEPARATOR . $resource;
        }
        return __DIR__ . $resource;
    }
}

if (!function_exists('ai_package_infos')) {
    /**
     * Package infos array.
     *
     * @return array
     */
    function ai_package_infos()
    {
        return ai_crud_infos();
    }
}

if (!function_exists('ai_crud_model_api')) {
    /**
     * Get API from model
     *
     * @return array
     */
    function ai_crud_model_api($modelClass)
    {
        $model = new $modelClass();
        return $model->getApi();
    }
}
