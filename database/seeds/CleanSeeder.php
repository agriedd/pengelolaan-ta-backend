<?php

use Illuminate\Database\Seeder;
use App\Model\{
	Admin, Jurusan, Dosen, Prodi,
    InformasiAdmin, InformasiJurusan, InformasiDosen, InformasiProdi
};
use DB;

class CleanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	InformasiAdmin::query()->truncate();
        Admin::query()->truncate();
        
        InformasiDosen::query()->truncate();
        Dosen::query()->truncate();

        InformasiProdi::query()->truncate();
        Prodi::query()->truncate();

        InformasiJurusan::query()->truncate();
    	Jurusan::query()->truncate();
    }
}
