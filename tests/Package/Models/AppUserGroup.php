<?php

namespace Antares\Tests\Package\Models;

use Antares\Crud\Metadata\DataSource\TableDataSource;
use Antares\Crud\Metadata\Layout\Field as LayoutField;
use Antares\Crud\Metadata\Layout\Fieldset;
use Antares\Crud\Metadata\Layout\HBox;
use Antares\Crud\Metadata\Order\Order;
use Antares\Tests\Package\Metadata\PkUnsignedBigintField;
use Antares\Tests\Package\Metadata\UnsignedBigintField;
use Antares\Tests\TestCase\Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserGroup extends AppModel
{
    use HasFactory;

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }

    protected $table = 'user_groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public function fieldsMetadata()
    {
        $usersMetadata = [
            'model' => AppUser::class,
            'showFields' => ['name'     => ['uicCols' => 3]],
            'showFields' => ['email'    => ['uicCols' => 3]],
        ];

        $groupsMetadata = [
            'model' => AppGroup::class,
            'showFields' => ['name' => ['uicCols' => 10]]
        ];

        return [
            PkUnsignedBigintField::make([
                'name' => 'id',
                'uicCols' => 2,
            ]),
            UnsignedBigintField::make([
                'name' => 'user_id',
                'label' => 'User',
                'tooltip' => 'User ID',
                'default' => request()->user()->id ?? null,
                'dataSource' => TableDataSource::make($usersMetadata),
            ]),

            UnsignedBigintField::make([
                'name' => 'group_id',
                'label' => 'Group',
                'tooltip' => 'Group ID',
                'dataSource' => TableDataSource::make($groupsMetadata),
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
            'group_id.id' => [
                'label' => 'Group',
                'width' => '5',
            ],
            'group_id.name' => [
                'label' => 'Group Name',
                'width' => '40',
            ],
            'user_id.id' => [
                'label' => 'User',
                'width' => '5',
            ],
            'user_id.name' => [
                'label' => 'User Name',
                'width' => '40',
            ],
        ];
    }

    public function filtersFieldsMetadata()
    {
        return [
            'id' => ['disabled' => false],
            'user_id',
            'group_id',
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
                ],
            ],
            'optionFields' => 'id|user_id.name|group_id.name',
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
                            LayoutField::make(['name' => 'user_id']),
                        ],
                    ]),
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'group_id']),
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
                            LayoutField::make(['name' => 'user_id']),
                            LayoutField::make(['name' => 'group_id']),
                        ],
                    ]),
                ],
            ])
        ];
    }
}
