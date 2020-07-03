<?php

namespace Antares\Crud;

use Illuminate\Database\Eloquent\Model;

class CrudModel extends Model
{
    public function defaultMetadata()
    {
        return [
            'filters' => null,
            'order' => empty($this->primaryKey) ? null : [['field' => $this->primaryKey, 'type' => 'asc']],
            'pagination' => [
                'per_page' => config('crud.model.metadata.pagination.per_page', 30),
            ],
        ];
    }

    protected $metadata;

    public function &metadata()
    {
        if (empty($metadata)) {
            $this->metadata = $this->defaultMetadata();
        }

        return $this->metadata;
    }
}
