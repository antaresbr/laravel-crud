<?php

namespace Antares\Tests\Feature\GroupCrud;

use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\GroupCrudAbstractTestCase;

class GroupCrudMetadataTest extends GroupCrudAbstractTestCase
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
        $this->assertNull(Arr::get($json, 'data.metadata.filters.static'));
        
        $this->assertTrue(Arr::has($json, 'data.metadata.filters.custom'));
        $this->assertNull(Arr::get($json, 'data.metadata.filters.custom'));

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
            'description' => [
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
                            [
                                'type' => 'field',
                                'name' => 'description',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'description',
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
                    'width' => 90,
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
                            [
                                'type' => 'field',
                                'name' => 'description',
                                'cols' => null,
                                'width' => null,
                                'height' => null,
                                'field' => 'description',
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
        $this->assertCount(4, $items);

        $item = $items[0];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'id',
            'label' => 'ID',
            'tooltip' => 'Generated ID',
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

        $item = $items[1];
        $this->assertIsArray($item);
        $this->assertEquals([
            'name' => 'name',
            'label' => 'Name',
            'tooltip' => 'Group name',
            'placeholder' => null,
            'type' => 'text',
            'length' => 255,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => 'upper',
            'uic' => 'text',
            'uicCols' => 5,
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
            'name' => 'description',
            'label' => 'Description',
            'tooltip' => 'Group description',
            'placeholder' => null,
            'type' => 'text',
            'length' => 255,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 12,
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
            'name' => 'virtualfield',
            'label' => 'Virtual Field',
            'tooltip' => null,
            'placeholder' => null,
            'type' => 'text',
            'length' => 255,
            'precision' => null,
            'unsigned' => true,
            'mask' => null,
            'letterCase' => null,
            'uic' => 'text',
            'uicCols' => 12,
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
            'virtual' => true,
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
                'description' => ['string', 'required'],
            ],
            'show' => [
                'id' => ['integer', 'required'],
            ],
            'update' => [
                'id' => ['integer', 'required', 'unique'],
                'name' => ['string', 'required', 'min:3', 'max:255'],
                'description' => ['string', 'required'],
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
        $this->assertNull(Arr::get($json, 'data.metadata.details'));
    }
}
