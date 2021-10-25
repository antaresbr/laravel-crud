<?php

namespace Antares\Crud\Http;

use Antares\Http\AbstractHttpErrors;

class CrudHttpErrors extends AbstractHttpErrors
{
    public const MENUID_NOT_DEFINED = 991001;
    public const ACTION_ERROR = 991002;
    public const PARTIALLY_SUCCESSFUL = 991003;
    public const ARRAY_LENGTHS_DIFFER = 991004;
    public const NO_PRIMARY_KEY_SUPPLIED = 991005;

    public const NO_DATA_SUPPLIED = 991011;
    public const DATA_VALIDATION_ERROR = 991012;
    public const DATA_MODEL_CREATE_FAIL = 991013;
    public const DATA_MODEL_QUERY_ERROR = 991014;
    public const DATA_MODEL_UPDATE_FAIL = 991015;
    public const DATA_MODEL_DELETE_FAIL = 991016;

    public const TARGET_DATA_MODEL_NOT_FOUND = 991021;
    public const TARGET_DATA_MODIFIED_BY_OTHERS = 991022;

    public const MESSAGES = [
        self::MENUID_NOT_DEFINED => 'crud::errors.menuid_not_defined',
        self::ACTION_ERROR => 'crud::errors.action_error',
        self::PARTIALLY_SUCCESSFUL => 'crud::errors.partially_successful',
        self::ARRAY_LENGTHS_DIFFER => 'crud::errors.array_lengths_differ',
        self::NO_PRIMARY_KEY_SUPPLIED => 'crud::errors.no_primary_key_supplied',

        self::NO_DATA_SUPPLIED => 'crud::errors.no_data_supplied',
        self::DATA_VALIDATION_ERROR => 'crud::errors.data_validation_error',
        self::DATA_MODEL_CREATE_FAIL => 'crud::errors.data_model_create_fail',
        self::DATA_MODEL_QUERY_ERROR => 'crud::errors.data_model_query_error',
        self::DATA_MODEL_UPDATE_FAIL => 'crud::errors.data_model_update_fail',
        self::DATA_MODEL_DELETE_FAIL => 'crud::errors.data_model_delete_fail',

        self::TARGET_DATA_MODEL_NOT_FOUND => 'crud::errors.target_data_model_not_found',
        self::TARGET_DATA_MODIFIED_BY_OTHERS => 'crud::errors.target_data_modified_by_others',
    ];
}
