<?php

namespace Antares\Crud\Metadata\Detail;

use Antares\Crud\CrudException;
use Antares\Crud\CrudModel;
use Antares\Crud\Metadata\AbstractMetadata;

class Detail extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'model' => [
                'type' => 'string|Antares\Crud\CrudModel',
                'required' => false,
                'nullable' => true,
            ],
            'table' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'api' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'title' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'bonds' => [
                'type' => 'array',
                'required' => true,
                'nullable' => false,
            ],
        ];
    }

    /**
     * @see AbstractMetadata::customDefaults()
     *
     * @return void
     */
    protected function customDefaults(array &$data)
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
            }

            unset($data['model']);
        }

        
        if ($model) {
            if (empty($data['table'])) {
                $data['table'] = $model->getTable();
            }
            if (empty($data['api'])) {
                $data['api'] = $model->getApi();
            }
        }
        if (empty($data['api']) and !empty($data['table'])) {
            $data['api'] = $data['table'];
        }

        parent::customDefaults($data);
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        //--[ bounds ]--
        if (!is_array($this->bonds)) {
            throw CrudException::forInvalidObjectType('array', $this->bonds);
        } else {
            $items = [];
            foreach ($this->bonds as $item) {
                if (is_array($item)) {
                    $item = Bond::make($item);
                }
                if (!($item instanceof Bond)) {
                    throw CrudException::forInvalidObjectType(Bond::class, $item);
                }
                $items[] = $item;
            }
            $this->bonds = $items;
        }
    }
}
