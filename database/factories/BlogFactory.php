<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Blog::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(4),
        'url' => $faker->domainName,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});