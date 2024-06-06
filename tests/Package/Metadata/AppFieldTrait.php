<?php

namespace Antares\Tests\Package\Metadata;

trait AppFieldTrait
{
    /**
     * Get valid field types
     *
     * @return array
     */
    protected function types()
    {
        return array_merge(parent::types(), [
            'email',
            'phone',
        ]);
    }
}
