<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            // primary key
            $table->id();

            // relationship
            $table->unsignedBigInteger('id_jurusan')->nullable();


            //others
            $table->string("username", 20)->unique();
            $table->string("password");


            //timestamps
            $table->timestamps();
            //soft deletes enable
            $table->softDeletes();
            //token enable
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
