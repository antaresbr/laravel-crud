<?php

namespace Antares\Crud;

use Illuminate\Database\Eloquent\Model;

class CrudModel extends Model
{
    public function defaultMetadata()
    {
        return [
            'filters' => [
                'static' => null,
                'custom' => null,
            ],
            'order' => empty($this->primaryKey) ? null : [['field' => $this->primaryKey, 'type' => 'asc']],
            'pagination' => [
                'per_page' => config('crud.model.metadata.pagination.per_page', 30),
            ],
            'table' => $this->table,
            'fields' => null,
        ];
    }

    protected $metadata;

    public function &metadata()
    {
        if (empty($this->metadata)) {
            $this->metadata = $this->defaultMetadata();
            if (method_exists($this, 'fieldsMetadata')) {
                $this->metadata['fields'] = $this->fieldsMetadata();
            } elseif (property_exists($this, 'fieldsMetadata')) {
                $this->metadata['fields'] = $this->fieldsMetadata;
            }
        }

        return $this->metadata;
    }
}
