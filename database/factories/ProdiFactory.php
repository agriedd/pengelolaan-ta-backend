<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Prodi;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Prodi::class, function (Faker $faker) {
    return [
        /**
         * factory migration untuk prodi
         * 
         * @property string nama
         * @property string kd_prodi
         * @property string keterangan
         * @property timestamps created_at
         * @property timestamps updated_at
         */

        "nama"			=> $faker->name,
        "kd_prodi"		=> $faker->username,
        "keterangan"	=> $faker->sentences(3),
        "created_at"	=> Carbon::now(),
        "updated_at"	=> Carbon::now(),
    ];
});
