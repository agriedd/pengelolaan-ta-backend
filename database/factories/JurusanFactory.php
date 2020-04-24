<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\{
	Jurusan,
	Prodi,
	Admin,
	InformasiAdmin,
};
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Jurusan::class, function (Faker $faker) {
    return [
        /**
         * factory model jurusan
         * 
         * @property string nama
         * @property string kd_jurusan
         * @property string keterangan
         * @property date created_at
         * @property date updated_at
         */
        // "nama"	=> ""
		"created_at"	=> Carbon::now(),
		"updated_at"	=> Carbon::now(),
    ];
});


