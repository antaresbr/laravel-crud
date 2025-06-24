<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;

class UserCrudMetadataTest extends UserCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'metadata';
    }

    /** @test */
    public function reset_database()
    {
        $this->resetDatabase();
    }

    /** @test */
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    /** @test */
    public function unauthenticated_get_metadata()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_first_user()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(1);
    }

    /** @test */
    public function get_metadata()
    {
        $this->bootstrapAndAuthUser();
        $this->metadataRequest_get();
    }

    /** @test */
    public function metadata_filters()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();
        $this->assertTrue(Arr::has($json, 'data.metadata.filters'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.filters'));
        
        $this->assertTrue(Arr::has($json, 'data.metadata.filters.static'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.filters.static'));
        $this->assertEquals([
            [
                'filters' => null,
                'column' => 'id',
                'operator' => 'between',
                'value' => 0,
                'endValue' => 65,
                'conjunction' => 'and',
                'uuid' => null,
            ],
            [
                'filters' => [
                    [
                        'filters' => null,
                        'column' => 'name',
                        'operator' => 'is not null',
                        'value' => null,
                        'endValue' => null,
                        'conjunction' => 'or',
                        'uuid' => null,
                    ],
                    [
                        'filters' => null,
                        'column' => 'email',
                        'operator' => 'ilike',
                        'value' => '%',
                        'endValue' => null,
                        'conjunction' => 'or',
                        'uuid' => null,
                    ],
                ],
                'column' => null,
                'operator' => '=',
                'value' => null,
                'endValue' => null,
                'conjunction' => 'and',
                'uuid' => null,
            ],
        ], Arr::get($json, 'data.metadata.filters.static'));

        $this->assertTrue(Arr::has($json, 'data.metadata.filters.custom'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.filters.custom'));
        $this->assertEquals([
            [
                'filters' => null,
                'column' => 'name',
                'operator' => 'like',
                'value' => '%',
                'endValue' => null,
                'conjunction' => 'and',
                'uuid' => null,
            ],
        ], Arr::get($json, 'data.metadata.filters.custom'));

        $this->assertEquals([
            'id' => [
                'uicProperties' => [
                    'action' => [],
                    'conditional' => [],
                ],
                'disabled' => false,
            ],
            'name' => [
                'uicProperties' => [
                    'action' => [],
                    'conditional' => [],
                ],
            ],
            'email' => [
                'uicProperties' => [
                    'action' => [],
                    'conditional' => [],
                ],
            ],
        ], Arr::get($json, 'data.metadata.filters.fields'));

        $this->assertTrue(Arr::has($json, 'data.metadata.filters.layout'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.filters.layout'));
        $this->assertEquals([
            [
                'type' => 'fieldset',
                'name' => 'fieldsetData',
                'cols' => null,
                'width' => null,
                'height' => null,
                'title' => 'Data',
                'border' => false,
                'children' => [
                    [
                        'type' => 'hbox',
                        'name' => null,
                        'cols' => 12,
                        'width' => null,
                        'height' => null,
                        'title' => null,
                        'border' => false,
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'id',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'id',
                            ],
                            [
                                'type' => 'field',
                                'name' => 'name',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'name',
                            ],
                        ],
                    ],
                    [
                        'type' => 'hbox',
                        'name' => null,
                        'cols' => 12,
                        'width' => null,
                        'height' => null,
                        'title' => null,
                        'border' => false,
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'email',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'email',
                            ],
                            [
                                'type' => 'field',
                                'name' => 'phone',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'phone',
                            ],
                        ],
                    ],
                ],
            ],
        ], Arr::get($json, 'data.metadata.filters.layout'));

        $this->assertTrue(Arr::has($json, 'data.metadata.filters.ignoreStatic'));
        $this->assertFalse(Arr::get($json, 'data.metadata.filters.ignoreStatic'));
    }

    /** @test */
    public function metadata_orders()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.orders'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.orders'));
        $this->assertEquals([
            [
                'field' => 'id',
                'type' => 'asc',
            ],
            [
                'field' => 'name',
                'type' => 'asc',
            ],
        ], Arr::get($json, 'data.metadata.orders'));
    }

    /** @test */
    public function metadata_pagination()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.pagination'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.pagination'));
        $this->assertEquals([
            'perPage' => 30,
        ], Arr::get($json, 'data.metadata.pagination'));
    }

    /** @test */
    public function metadata_grid()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.grid'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.grid'));
        $this->assertEquals([
            'fields' => [
                'id' => [
                    'label' => 'ID',
                    'width' => 10,
                ],
                'name' => [
                    'label' => 'Name',
                    'width' => 35,
                ],
                'email' => [
                    'label' => 'e-mail',
                    'width' => 30,
                ],
                'phone' => [
                    'label' => 'Phone',
                    'width' => 25,
                ],
            ],
        ], Arr::get($json, 'data.metadata.grid'));
    }

    /** @test */
    public function metadata_layout()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.layout'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.layout'));
        $this->assertEquals([
            [
                'type' => 'fieldset',
                'name' => 'fieldsetData',
                'cols' => null,
                'width' => null,
                'height' => null,
                'title' => 'Data',
                'border' => false,
                'children' => [
                    [
                        'type' => 'hbox',
                        'name' => null,
                        'cols' => 12,
                        'width' => null,
                        'height' => null,
                        'title' => null,
                        'border' => false,
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'id',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'id',
                            ],
                            [
                                'type' => 'field',
                                'name' => 'name',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'name',
                            ],
                        ],
                    ],
                    [
                        'type' => 'hbox',
                        'name' => null,
                        'cols' => 12,
                        'width' => null,
                        'height' => null,
                        'title' => null,
                        'border' => false,
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'email',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'email',
                            ],
                            [
                                'type' => 'field',
                                'name' => 'password',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'password',
                            ],
                        ],
                    ],
                    [
                        'type' => 'hbox',
                        'name' => null,
                        'cols' => 12,
                        'width' => null,
                        'height' => null,
                        'title' => null,
                        'border' => false,
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'phone',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'phone',
                            ],
                        ],
                    ],
                ],
            ],
        ], Arr::get($json, 'data.metadata.layout'));
    }

    /** @test */
    public function metadata_fields()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.fields'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.fields'));
        $items = Arr::get($json, 'data.metadata.fields');
        $this->assertIsArray($items);
        $this->assertCount(7, $items);

        $item = $items[0];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'id',
            'label' => 'ID',
            'tooltip' => 'User ID',
            'placeholder' => null,
            'type' => 'bigint',
            'length' => null,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 2,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [
                    [
                        'action' => 'new',
                        'property' => 'hidden',
                        'value' => true
                    ],
                    [
                        'action' => 'update',
                        'property' => 'disabled',
                        'value' => true
                    ],
                ],
                'conditional' => [
                    [
                        'property' => 'disabled',
                        'condition' => 'this.form.controls.control.value == 0',
                        'onTrue' => true,
                        'onFalse' => null
                    ],
                    [
                        'property' => 'hidden',
                        'condition' => 'this.form.controls.control.value != 0',
                        'onTrue' => false,
                        'onFalse' => '__not-used__',
                    ],
                ],
            ],
            'dataSource' => null,
            'disabled' => true,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[1];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'name',
            'label' => 'Name',
            'tooltip' => 'User name',
            'placeholder' => null,
            'type' => 'text',
            'length' => 255,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 7,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => false,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[2];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'email',
            'label' => 'e-mail',
            'tooltip' => 'User e-mail',
            'placeholder' => null,
            'type' => 'email',
            'length' => null,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 6,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => false,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[3];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'password',
            'label' => 'Password',
            'tooltip' => 'User password',
            'placeholder' => null,
            'type' => 'text',
            'length' => 255,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'password',
            'uicCols' => 3,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => false,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[4];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'email_verified_at',
            'label' => 'Verified at',
            'tooltip' => 'Timestamp of e-mail verification',
            'placeholder' => null,
            'type' => 'timestamp',
            'length' => null,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 3,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => true,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[5];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'remember_token',
            'label' => 'Token',
            'tooltip' => 'Access token',
            'placeholder' => null,
            'type' => 'text',
            'length' => 100,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 6,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => true,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);

        $item = $items[6];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'phone',
            'label' => 'Phone',
            'tooltip' => 'Phone number',
            'placeholder' => null,
            'type' => 'phone',
            'length' => 20,
            'precision' => null,
            'unsigned' => true,
            'mask' => '+00 (00) 00000-0000||+00 (00) 0000-0000',
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 3,
            'uicWidth' => null,
            'uicHeight' => null,
            'uicPattern' => null,
            'uicProperties' => [
                'action' => [],
                'conditional' => [],
            ],
            'dataSource' => null,
            'disabled' => false,
            'hidden' => false,
            'virtual' => false,
            'default' => null,
            'gridCols' => null,
        ], $item);
    }

    /** @test */
    public function metadata_picklists()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.picklists'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.picklists'));
        $this->assertEquals([], Arr::get($json, 'data.metadata.picklists'));
    }

    /** @test */
    public function metadata_rules()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.rules'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.rules'));
        $this->assertEquals([
            'index' => [],
            'store' => [
                'id' => ['integer', 'unique', 'nullable'],
                'name' => ['string', 'required', 'min:3', 'max:255'],
                'email' => ['string', 'required'],
                'phone' => ['string', 'nullable'],
            ],
            'show' => [
                'id' => ['integer', 'required'],
            ],
            'update' => [
                'id' => ['integer', 'required', 'unique'],
                'name' => ['string', 'required', 'min:3', 'max:255'],
                'email' => ['string', 'required'],
                'phone' => ['string', 'nullable'],
            ],
            'destroy' => [
                'id' => ['integer', 'required'],
            ],
        ], Arr::get($json, 'data.metadata.rules'));
    }

    /** @test */
    public function metadata_details()
    {
        $this->bootstrapAndAuthUser();
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertTrue(Arr::has($json, 'data.metadata.details'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.details'));
        $this->assertEquals([
            [
                'model' => null,
                'table' => 'user_groups',
                'api' => 'api/tests/package/user_groups',
                'menuPath' => null,
                'title' => 'Groups',
                'bonds' => [
                    [
                        'detail' => ['user_id'],
                        'master' => ['id'],
                    ],
                ],
            ],
        ], Arr::get($json, 'data.metadata.details'));
    }
}
