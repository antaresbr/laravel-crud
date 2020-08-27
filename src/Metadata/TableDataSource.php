<?php

namespace Antares\Crud\Metadata;

use Antares\Crud\CrudException;
use Antares\Crud\CrudModel;

class TableDataSource extends DataSource
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return staic
     */
    public static function make(array $data)
    {
        if ($model = array_key_exists('model', $data)) {
            $model = $data['model'];

            if (!is_null($model)) {
                if (is_string($model)) {
                    $model = new $model();
                }
                if (!($model instanceof CrudModel)) {
                    throw CrudException::forInvalidObjectType(CrudModel::class, $model);
                }

                $meta = null;
                if (method_exists($model, 'asDataSourceMetadata')) {
                    $meta = $model->asDataSourceMetadata();
                } elseif (property_exists($model, 'asDataSourceMetadata')) {
                    $meta = $model->asDataSourceMetadata;
                } else {
                    $meta = [
                        'id' => $model->table,
                        'sourceKey' => $model->primaryKey,
                    ];
                }

                $data = array_merge($meta, $data);
            }

            unset($data['model']);
        }

        $data['type'] = 'table';

        return parent::make($data);
    }
}
