<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformasiJurusan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informasi_jurusan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_jurusan");
            $table->foreign("id_jurusan")->references("id")->on("jurusan")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            $table->string("email")->nullable();
            $table->string("website")->nullable();
            $table->text("keterangan")->nullable();
            $table->text("media_sosial")->nullable();

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
        Schema::dropIfExists('informasi_jurusan');
    }
}
