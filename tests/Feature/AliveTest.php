<?php

namespace Antares\Tests\Feature;

use Antares\Tests\Package\TestCase;
use Antares\Foundation\Arr;
use PHPUnit\Framework\Attributes\Test;

class AliveTest extends TestCase
{
    #[Test]
    public function get_alive()
    {
        $response = $this->get(config('testcase.route.prefix.api') . '/alive');
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('package', $json);
        $this->assertArrayHasKey('env', $json);
        $this->assertArrayHasKey('serverDateTime', $json);
        
        $infos = ai_crud_infos();
        $this->assertEquals($infos->name, Arr::get($json, 'package.name'));
        $this->assertEquals($infos->version->major, Arr::get($json, 'package.version.major'));
        $this->assertEquals($infos->version->release, Arr::get($json, 'package.version.release'));
        $this->assertEquals($infos->version->minor, Arr::get($json, 'package.version.minor'));
    }
}
