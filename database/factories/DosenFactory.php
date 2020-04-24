<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Dosen;
use Faker\Generator as Faker;

$factory->define(Dosen::class, function (Faker $faker) {
    return [
        /**
         * dosen migrasi
         * 
         * @property string username
         * @property string password
         * @property integer id_prodi
         */
        "username"	=> $faker->username,
        "password"	=> app("hash")->make("password"),
    ];
});
