<?php

namespace Antares\Tests\Package\AbstractTestCases;

use Antares\Tests\Package\TestCase;
use Antares\Foundation\Arr;

abstract class HandlerAbstractTestCase extends TestCase
{
    protected function assertMetadataStructureFromResponse($response) {
        $metadata = json_decode($response->getContent(), true);
        $this->assertIsArray($metadata);
        $this->assertTrue(Arr::has($metadata, 'status'));
        $this->assertTrue(Arr::has($metadata, 'message'));
        $this->assertIsArray(Arr::get($metadata, 'data'));
        $this->assertTrue(Arr::has($metadata, 'data.action'));
        $this->assertIsArray(Arr::get($metadata, 'data.metadata'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.api'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.table'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.primaryKey'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.filters'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.orders'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.pagination'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.grid'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.layout'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.menu'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.fields'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.picklists'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.rules'));
        $this->assertTrue(Arr::has($metadata, 'data.metadata.details'));
    }
}
