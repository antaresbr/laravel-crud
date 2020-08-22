<?php

namespace Antares\Crud;

use Antares\Crud\Metadata\Field;
use Antares\Crud\Metadata\Order;
use Illuminate\Database\Eloquent\Model;

class CrudModel extends Model
{
    public function defaultMetadata()
    {
        return [
            'table' => $this->table,
            'filters' => [
                'static' => null,
                'custom' => null,
                'fields' => null,
            ],
            'orders' => empty($this->primaryKey) ? null : [['field' => $this->primaryKey, 'type' => 'asc']],
            'pagination' => [
                'perPage' => config('crud.model.metadata.pagination.perPage', 30),
            ],
            'fields' => null,
            'picklists' => null,
            'rules' => null,
        ];
    }

    protected $metadata;

    public function &metadata(bool $getFields = true, bool $getOrders = true)
    {
        if (empty($this->metadata)) {
            $this->metadata = $this->defaultMetadata();

            if (!$getFields) {
                $this->metadata['fields'] = null;
            } else {
                $source = false;
                if (method_exists($this, 'fieldsMetadata')) {
                    $source = $this->fieldsMetadata();
                } elseif (property_exists($this, 'fieldsMetadata')) {
                    $source = $this->fieldsMetadata;
                }
                if ($source !== false) {
                    if (is_null($source)) {
                        $this->metadata['fields'] = null;
                    } else {
                        $this->metadata['fields'] = [];
                        foreach ($source as $item) {
                            if (!($item instanceof Field)) {
                                throw CrudException::forInvalidObjectType(Field::class, is_object($item) ? get_class($item) : gettype($item));
                            }
                            $this->metadata['fields'][] = $item;
                        }
                    }
                }
            }

            if (!$getOrders) {
                $this->metadata['orders'] = null;
            } else {
                $source = false;
                if (method_exists($this, 'ordersMetadata')) {
                    $source = $this->ordersMetadata();
                } elseif (property_exists($this, 'ordersMetadata')) {
                    $source = $this->ordersMetadata;
                }
                if ($source !== false) {
                    if (is_null($source)) {
                        $this->metadata['orders'] = null;
                    } else {
                        $this->metadata['orders'] = [];
                        foreach ($source as $item) {
                            if (!($item instanceof Order)) {
                                throw CrudException::forInvalidObjectType(Order::class, is_object($item) ? get_class($item) : gettype($item));
                            }
                            $this->metadata['orders'][] = $item;
                        }
                    }
                }
            }
        }

        return $this->metadata;
    }
}
