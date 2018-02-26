<?php

use App\Blog;
use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(),
        'local_id' => $faker->randomNumber(),
        'link' => $faker->url,
        'datetime_utc' => $faker->dateTimeThisDecade(),
        'blog_id' => function() {
            return factory(Blog::class)->create()->id;
        }
    ];
});
