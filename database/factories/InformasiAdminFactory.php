<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\InformasiAdmin;
use Faker\Generator as Faker;

$factory->define(InformasiAdmin::class, function (Faker $faker) {
    return [
        "level" 	=> "0",
        "status"	=> "1"
    ];
});
