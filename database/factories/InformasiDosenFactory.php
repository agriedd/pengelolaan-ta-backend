<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\InformasiDosen;
use Faker\Generator as Faker;

$factory->define(InformasiDosen::class, function (Faker $faker) {
    return [
        /**
         * @property string nama
         * @property string nip
         * @property string jenis_kelamin
         * @property string tempat_lahir
         * @property date tanggal_lahir
         * @property string alamat
         * @property string agama
         * @property string email
         * @property string telepon
         * @property string media_sosial
         * @property string biodata
         * @property strign foto
         */

        "nama"			=> $faker->name,
        "jenis_kelamin"	=> $faker->randomElement(["L","P"]),
        "tempat_lahir"	=> $faker->address,
        "tanggal_lahir"	=> $faker->date("Y-m-d", 'now'),
        'alamat'		=> $faker->address,
        "agama"			=> $faker->randomElement(["1","2","3","4","5"]),
        "email"			=> $faker->email,
        "telepon"		=> $faker->phoneNumber,
        "biodata"		=> $faker->sentence(5, true),
    ];
});
