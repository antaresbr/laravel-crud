<?php

namespace Antares\Tests\Package\Models;

use Antares\Crud\Metadata\Detail\Bond;
use Antares\Crud\Metadata\Detail\Detail;
use Antares\Crud\Metadata\Field\UicActionProperty;
use Antares\Crud\Metadata\Field\UicConditionalProperty;
use Antares\Crud\Metadata\Field\UicProperties;
use Antares\Crud\Metadata\Filter\Filter;
use Antares\Crud\Metadata\Frame;
use Antares\Crud\Metadata\Layout\Field as LayoutField;
use Antares\Crud\Metadata\Layout\Fieldset;
use Antares\Crud\Metadata\Layout\HBox;
use Antares\Crud\Metadata\Order\Order;
use Antares\Tests\Package\Database\Factories\AppUserFactory;
use Antares\Tests\Package\Metadata\EmailField;
use Antares\Tests\Package\Metadata\PasswordField;
use Antares\Tests\Package\Metadata\PhoneField;
use Antares\Tests\Package\Metadata\PkUnsignedBigintField;
use Antares\Tests\Package\Metadata\TextField;
use Antares\Tests\Package\Metadata\TimestampField;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUser extends AppModel implements AuthenticatableContract
{
    use Authenticatable;
    use HasFactory;

    protected static function newFactory()
    {
        return AppUserFactory::new();
    }
    
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'phone',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function fieldsMetadata()
    {
        return [
            PkUnsignedBigintField::make([
                'name' => 'id',
                'tooltip' => 'User ID',
                'uicProperties' => UicProperties::make([
                    'action' => [
                        UicActionProperty::make([
                            'action' => 'new',
                            'property' => 'hidden',
                            'value' => true,
                        ]),
                        UicActionProperty::make([
                            'action' => 'update',
                            'property' => 'disabled',
                            'value' => true,
                        ]),
                    ],
                    'conditional' => [
                        UicConditionalProperty::make([
                            'property' => 'disabled',
                            'condition' => 'this.form.controls.control.value == 0',
                            'onTrue' => true,
                            'onFalse' => false,
                        ]),
                        UicConditionalProperty::make([
                            'property' => 'hidden',
                            'condition' => 'this.form.controls.control.value != 0',
                            'onTrue' => false,
                        ]),
                    ],
                ]),
            ]),
            TextField::make([
                'name' => 'name',
                'label' => 'Name',
                'tooltip' => 'User name',
                'length' => 255,
                'uicCols' => 7,
            ]),
            EmailField::make([
                'name' => 'email',
                'label' => 'e-mail',
                'tooltip' => 'User e-mail',
                'uicCols' => 6,
            ]),
            PasswordField::make([
                'name' => 'password',
                'tooltip' => 'User password',
                'uicCols' => 3,
            ]),
            TimestampField::make([
                'name' => 'email_verified_at',
                'label' => 'Verified at',
                'tooltip' => 'Timestamp of e-mail verification',
                'uicCols' => 3,
                'disabled' => true,
            ]),
            TextField::make([
                'name' => 'remember_token',
                'label' => 'Token',
                'tooltip' => 'Access token',
                'length' => 100,
                'uicCols' => 6,
                'disabled' => true,
            ]),

            PhoneField::make([
                'name' => 'phone',
                'label' => 'Phone',
                'tooltip' => 'Phone number',
                'uicCols' => 3,
            ]),
        ];
    }

    public function ordersMetadata()
    {
        return [
            Order::make(['field' => 'id', 'type' => 'asc']),
            Order::make(['field' => 'name', 'type' => 'asc']),
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
                'width' => '35',
            ],
            'email' => [
                'label' => 'e-mail',
                'width' => '30',
            ],
            'phone' => [
                'label' => 'Phone',
                'width' => '25',
            ],
        ];
    }

    public function filtersStaticMetadata()
    {
        return [
            Filter::make(['column' => 'id', 'operator' => 'between', 'value' => 0, 'endValue' => 65]),
            Filter::make(['filters' => [
                Filter::make(['column' => 'name', 'operator' => 'is not null', 'conjunction' => 'or']),
                Filter::make(['column' => 'email', 'operator' => 'ilike', 'value' => '%', 'conjunction' => 'or']),
            ]]),
        ];
    }

    public function filtersCustomMetadata()
    {
        return [
            Filter::make(['column' => 'name', 'operator' => 'like', 'value' => '%']),
        ];
    }

    public function filtersFieldsMetadata()
    {
        return [
            'id' => ['disabled' => false],
            'name',
            'email',
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
                'email' => [
                    'uic' => 'text',
                ],
            ],
            'optionFields' => 'id|name|email',
            'frame' => Frame::make([
                'title' => 'User Query',
                'size' => 'lg',
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
                        ],
                    ]),
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'email']),
                            LayoutField::make(['name' => 'password']),
                        ],
                    ]),
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'phone']),

                        ],
                    ]),
                ],
            ]),
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
                        ],
                    ]),
                    HBox::make([
                        'children' => [
                            LayoutField::make(['name' => 'email']),
                            LayoutField::make(['name' => 'phone']),
                        ],
                    ]),
                ],
            ]),
        ];
    }

    public function detailsMetadata()
    {
        return [
            Detail::make([
                'model' => AppUserGroup::class,
                'title' => 'Groups',
                'api' => ai_crud_model_api(AppUserGroup::class),
                'bonds' => [
                    Bond::make(['detail' => 'user_id', 'master' => 'id']),
                ],
            ]),
        ];
    }
}
