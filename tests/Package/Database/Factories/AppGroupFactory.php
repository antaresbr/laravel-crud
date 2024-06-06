<?php

namespace Antares\Tests\Package\Database\Factories;

use Antares\Tests\Package\Models\AppGroup;
use Antares\Tests\TestCase\Database\Factories\GroupFactory;

class AppGroupFactory extends GroupFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = parent::definition();
        $data['name'] = strtoupper($data['name']);
        return $data;
    }
}
