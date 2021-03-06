<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_prodi");
            $table->foreign("id_prodi")->references("id")->on("prodi")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            // $table->string("nip", 18)->nullable();
            $table->string("username")->unique();
            $table->string("password");

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
        Schema::dropIfExists('dosen');
    }
}
