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
    	// factory(\App\Model\Admin::class, 1)->create([
     //        'username'  => "agriedd",
     //    ])->each(function($admin){
     //        $admin->informasi()->saveMany(
     //            factory(\App\Model\InformasiAdmin::class, 1)->make()
     //        );
     //    });

        $admin = \App\Model\Admin::create([ "username" => "agriedd", "password" => app("hash")->make("password") ]);
        $admin->informasi()->create( [ "level" => 1 ] );

        $this->call(JurusanSeeder::class);
    }
}
