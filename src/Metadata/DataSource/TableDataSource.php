<?php

namespace Antares\Crud\Metadata\DataSource;

use Antares\Crud\CrudException;
use Antares\Crud\CrudModel;
use Antares\Crud\Metadata\Filter\Filter;

class TableDataSource extends DataSource
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return array_merge(parent::prototype(), [
            'filters' => [
                'type' => 'array',
                'required' => false,
                'nullable' => true,
                'default' => [],
            ],
        ]);
    }

    /**
     * @see AbstractMetadata::customValidations()
     */
    protected function customValidations()
    {
        parent::customValidations();

        //--[ filters ]--

        if (!empty($this->filters)) {
            if (!is_array($this->filters)) {
                throw CrudException::forInvalidObjectType('array', $this->filters);
            } else {
                foreach ($this->filters as $filter) {
                    if (!($filter instanceof Filter)) {
                        throw CrudException::forInvalidObjectType(Filter::class, $filter);
                    }
                }
            }
        }
    }

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

                $meta = $model->asDataSourceMetadata();

                if (empty($meta)) {
                    $meta = [
                        'id' => $model->getTable(),
                        'sourceKey' => $model->primaryKey,
                    ];
                }

                if (!isset($meta['metadata']) or $meta['metadata'] === true or $meta['metadata'] === null) {
                    $meta['metadata'] = $model->metadata([
                        'getLayout' => false,
                        'getMenu' => false,
                    ]);
                }

                $data = array_merge($meta, $data);
            }

            unset($data['model']);
        }

        $data['type'] = 'table';

        return parent::make($data);
    }
}
