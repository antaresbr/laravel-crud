<?php

namespace Antares\Crud\Metadata\Detail;

use Antares\Crud\CrudException;
use Antares\Crud\Metadata\AbstractMetadata;

class Bond extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'detail' => [
                'type' => 'string|array',
                'required' => true,
                'nullable' => false,
            ],
            'master' => [
                'type' => 'string|array',
                'required' => true,
                'nullable' => false,
            ],
        ];
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        if (is_string($this->detail)) {
            $this->detail = explode('|', $this->detail);
        }

        if (is_string($this->master)) {
            $this->master = explode('|', $this->master);
        }

        if (count($this->detail) != count($this->master)) {
            throw CrudException::forDifferentArrayLengths($this->detail, $this->master);
        }
    }
}
