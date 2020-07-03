<?php

namespace Antares\Crud\Http;

use Antares\Http\AbstractHttpErrors;

class CrudHttpErrors extends AbstractHttpErrors
{
    public const DATA_VALIDATION_ERROR = 991001;
    public const DATA_MODEL_CREATION_ERROR = 991011;
    public const DATA_MODEL_QUERY_ERROR = 991012;
    public const TARGET_DATA_MODEL_NOT_FOUND = 991013;

    public const MESSAGES = [
        self::DATA_VALIDATION_ERROR => 'Data validation error',
        self::DATA_MODEL_CREATION_ERROR => 'Data model creation error',
        self::DATA_MODEL_QUERY_ERROR => 'Data model query error',
        self::TARGET_DATA_MODEL_NOT_FOUND => 'Target data model not found',
    ];
}
