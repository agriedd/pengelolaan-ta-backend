<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRiwayatLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_login', function (Blueprint $table) {

            //the primary
            $table->id();

            //index column
            $table->nullableMorphs("user");

            //commons
            $table->string("username");
            $table->string("password")->nullable();
            $table->enum("status", [0,1])->default(0);
            $table->string("token")->nullable();

            $table->string("ip_address")->nullable();
            $table->text("headers")->nullable();

            $table->datetime("expired_at")->nullable();

            //additonal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_login');
    }
}
