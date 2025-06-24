<?php

namespace Antares\Tests\Package\Models;

use Antares\Crud\Metadata\Frame;
use Antares\Crud\Metadata\Layout\Field as LayoutField;
use Antares\Crud\Metadata\Layout\Fieldset;
use Antares\Crud\Metadata\Layout\HBox;
use Antares\Crud\Metadata\Order\Order;
use Antares\Tests\Package\Database\Factories\AppGroupFactory;
use Antares\Tests\Package\Metadata\PkUnsignedBigintField;
use Antares\Tests\Package\Metadata\TextField;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppGroup extends AppModel
{
    use HasFactory;

    protected static function newFactory()
    {
        return AppGroupFactory::new();
    }

    protected $table = 'groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
    ];

    public function fieldsMetadata()
    {
        return [
            PkUnsignedBigintField::make([
                'name' => 'id',
                'uicCols' => 2,
            ]),
            TextField::make([
                'name' => 'name',
                'label' => 'Name',
                'tooltip' => 'Group name',
                'length' => 255,
                'uicCols' => 5,
                'letterCase' => 'upper',
            ]),
            TextField::make([
                'name' => 'description',
                'label' => 'Description',
                'tooltip' => 'Group description',
                'length' => 255,
                'uicCols' => 12,
            ]),
            TextField::make([
                'name' => 'virtualfield',
                'label' => 'Virtual Field',
                'length' => 255,
                'uicCols' => 12,
                'virtual' => true,
            ]),
        ];
    }

    public function ordersMetadata()
    {
        return [
            Order::make(['field' => 'id', 'type' => 'asc']),
        ];
    }

    public function gridFieldsMetadata()
    {
        return [
            'id' => [
                'label' => 'ID',
                'width' => '10',
            ],
            'name' => [
                'label' => 'Name',
                'width' => '90',
            ],
        ];
    }

    public function filtersFieldsMetadata()
    {
        return [
            'id' => ['disabled' => false],
            'name',
            'description',
        ];
    }

    public function asDataSourceMetadata()
    {
        return [
            'id' => $this->table,
            'sourceKey' => $this->primaryKey,
            'api' => $this->getApi(),
            'showFields' => [
                'name' => [
                    'uic' => 'text',
                    'uicCols' => 4,
                ],
            ],
            'optionFields' => 'id|name',
            'frame' => Frame::make([
                'title' => 'Groups search',
                'size' => 'md',
                'backdrop' => 'static',
            ]),
        ];
    }

    public function layoutMetadata()
    {
        return [
            Fieldset::make([
                'name' => 'fieldsetData',
                'title' => 'Data',
                'children' => [
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'id']),
                            LayoutField::make(['name' => 'name']),
                            LayoutField::make(['name' => 'description']),
                        ],
                    ]),
                ],
            ])
        ];
    }

    public function filtersLayoutMetadata()
    {
        return [
            Fieldset::make([
                'name' => 'fieldsetData',
                'title' => 'Data',
                'children' => [
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'id']),
                            LayoutField::make(['name' => 'name']),
                            LayoutField::make(['name' => 'description']),
                        ],
                    ]),
                ],
            ])
        ];
    }

    public function calculateVirtualFields(array &$data, $metadata = null)
    {
        if (empty($data)) {
            return;
        }

        if (!is_array($metadata)) {
            $metadata = $this->getFieldsMetadata(true);
        }

        foreach($data as &$item) {
            $item['virtualfield'] = 'virtual value ' . $item['id'];
        }
    }
}
