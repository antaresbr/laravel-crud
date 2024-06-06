<?php

namespace Antares\Tests\Package;

use Antares\Crud\Providers\CrudServiceProvider;
use Antares\Tests\Package\Providers\PackageServiceProvider;
use Antares\Tests\TestCase\AbstractTestCase;

class TestCase extends AbstractTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $providers = array_merge(parent::getPackageProviders($app) ?? [], [
            PackageServiceProvider::class,
            CrudServiceProvider::class,
        ]);
        return array_unique($providers);
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
