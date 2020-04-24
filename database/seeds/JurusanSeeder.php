<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Model\{
	Jurusan,
	Prodi,
	Admin,
	InformasiAdmin,
};
use Illuminate\Database\Eloquent\Factory;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
    	/**
         * factory model jurusan
         * 
         * @property string nama
         * @property string kd_jurusan
         * @property string keterangan
         * @property date created_at
         * @property date updated_at
         */
    	$list_jurusan = [
    		collect([
    			"nama"			=> "Teknik Elektro",
    			"kd_jurusan"	=> "Elektro",
    			"keterangan"	=> "Jurusan teknik elektro, PNK",
    		]),
    		collect([
    			"nama"			=> "Teknik Mesin",
    			"kd_jurusan"	=> "Mesin",
    			"keterangan"	=> "Jurusan teknik mesin, PNK",
    		]),
    	];

    	foreach ($list_jurusan as $key => $jurusan) {
    		factory(Jurusan::class, 1)->create($jurusan->all())->each(function($jurusan){
                $jurusan->prodi()->saveMany(factory(Prodi::class, 3)->make());
                $jurusan->admin()->saveMany(
                        factory(Admin::class, 1)->make()
                    )->each(function($admin){
                        $admin->informasi()->saveMany(
                            factory(InformasiAdmin::class, 1)->make()
                        );
                    });
            });
    	}
    }
}
