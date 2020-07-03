<?php

namespace Antares\Http\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
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
        return [];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Log text
     *
     * @param string $text
     * @param string $title
     * @return void
     */
    protected function logText($text, $title)
    {
        if (strlen($title) > 0) {
            echo PHP_EOL . "--[ {$title} ]--" . PHP_EOL;
        }
        echo "{$text}" . PHP_EOL;
    }
}
