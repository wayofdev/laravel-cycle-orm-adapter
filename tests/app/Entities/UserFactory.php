<?php

declare(strict_types=1);

namespace WayOfDev\App\Entities;

use Illuminate\Support\Str;
use WayOfDev\Cycle\Bridge\Laravel\Factories\Factory;

final class UserFactory extends Factory
{
    /**
     * @phpstan-ignore-next-line
     */
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'company' => $this->faker->company(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'friends' => [],
            'roles' => collect(),
            'friendsAsIlluminateCollection' => collect(),
        ];
    }
}
