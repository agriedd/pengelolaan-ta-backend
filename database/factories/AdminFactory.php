<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Admin;
use Faker\Generator as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'username' => $faker->username,
        'password' => Hash::make('password'),
        'created_at' => Carbon::now(),
    ];
});
