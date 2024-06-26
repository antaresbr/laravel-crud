<?php

namespace Antares\Crud\Metadata\Filter;

use Antares\Crud\CrudException;
use Antares\Crud\Metadata\AbstractMetadata;
use Antares\Foundation\Arr;
use Antares\Foundation\Str;
use Illuminate\Database\Eloquent\Model;

class Filter extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'filters' => [
                'type' => 'array',
                'default' => null,
            ],
            'column' => [
                'type' => 'string',
                'default' => null,
            ],
            'operator' => [
                'type' => 'string',
                'values' => [
                    '=', '!=', '>', '<', '>=', '<=', '<>',
                    'in', 'not in', 'between', 'not between',
                    'is null', 'is not null',
                    'like', 'not like', 'ilike', 'not ilike',
                    'similar to', 'not similar to',
                ],
                'default' => '=',
            ],
            'value' => [
                'type' => 'mixed',
                'default' => null,
            ],
            'endValue' => [
                'type' => 'mixed',
                'default' => null,
            ],
            'conjunction' => [
                'type' => 'string',
                'values' => ['and', 'or'],
                'default' => 'and',
            ],
            'uuid' => [
                'type' => 'string',
                'default' => null,
            ],
        ];
    }

    /**
     * @see AbstractMetadata::customValidations()
     */
    protected function customValidations()
    {
        if (empty($this->filters) and empty($this->column)) {
            throw FilterException::forPropertyNotSupplied('[ column | filters ]');
        }
        if (!empty($this->filters) and !empty($this->column)) {
            throw FilterException::forMutuallyExclusiveProperties('[ column | filters ]');
        }

        $source = $this->filters;
        if (!empty($source)) {
            $filters = [];
            foreach ($source as $item) {
                if (is_array($item)) {
                    $item = Filter::make($item);
                }
                if (!($item instanceof Filter)) {
                    throw CrudException::forInvalidObjectType(Filter::class, $item);
                }
                $filters[] = $item;
            }
            $this->filters = $filters;
        }

        if (!empty($this->column)) {
            if (!$this->options->has('value') and !Str::icIn($this->operator, 'is null', 'is not null')) {
                throw FilterException::forPropertyNotSupplied('value');
            }
            if (Str::icIn($this->operator, 'between', 'not between') and !$this->options->has('endValue')) {
                throw FilterException::forPropertyNotSupplied('endValue');
            }
        }

        if (Str::icIn('in', 'not in') and !is_array($this->value)) {
            $this->value = [$this->value];
        }
    }

    public function normalizeToDatabaseDriver(Model $model)
    {
        $dbDriverName = $model->getConnection()->getDriverName();
        
        if (in_array($dbDriverName, ['mysql', 'sqlite'])) {
            $this->operator = ($this->operator == 'ilike') ? 'like' : $this->operator;
            $this->operator = ($this->operator == 'not ilike') ? 'not like' : $this->operator;
        }
    }
}
