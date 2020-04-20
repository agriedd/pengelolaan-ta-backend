<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
    	factory(\App\Model\Admin::class, 1)->create()->each(function($admin){
            $admin->informasi()->saveMany(
                factory(\App\Model\InformasiAdmin::class, 1)->make()
            );
        });
    }
}
