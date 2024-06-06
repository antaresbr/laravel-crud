<?php

namespace Antares\Tests\Package\Database\Factories;

use Antares\Tests\Package\Models\AppUser;
use Antares\Tests\TestCase\Database\Factories\UserFactory;

class AppUserFactory extends UserFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = parent::definition();
        $data['phone'] = $this->faker->numerify('+## (##) #####-####');
        return $data;
    }
}
