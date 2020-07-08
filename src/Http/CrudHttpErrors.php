<?php

namespace Antares\Crud\Http;

use Antares\Http\AbstractHttpErrors;

class CrudHttpErrors extends AbstractHttpErrors
{
    public const MENUID_NOT_DEFINED = 991001;

    public const DATA_VALIDATION_ERROR = 991011;
    public const DATA_MODEL_CREATION_ERROR = 991012;
    public const DATA_MODEL_QUERY_ERROR = 991013;
    public const DATA_MODEL_UPDATE_FAIL = 991014;
    public const DATA_MODEL_DELETE_FAIL = 991015;

    public const TARGET_DATA_MODEL_NOT_FOUND = 991021;
    public const TARGET_DATA_MODIFIED_BY_OTHERS = 991022;

    public const MESSAGES = [
        self::MENUID_NOT_DEFINED => 'MenuId not defined',
        self::DATA_VALIDATION_ERROR => 'Data validation error',
        self::DATA_MODEL_CREATION_ERROR => 'Data model creation error',
        self::DATA_MODEL_QUERY_ERROR => 'Data model query error',
        self::DATA_MODEL_UPDATE_FAIL => 'Data model update fail',
        self::DATA_MODEL_DELETE_FAIL => 'Data model delete fail',
        self::TARGET_DATA_MODEL_NOT_FOUND => 'Target data model not found',
        self::TARGET_DATA_MODIFIED_BY_OTHERS => 'Target data model was modified by others',
    ];
}
