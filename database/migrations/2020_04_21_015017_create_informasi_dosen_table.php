<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformasiDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informasi_dosen', function (Blueprint $table) {
            $table->id();

            //relationship
            $table->unsignedBigInteger("id_dosen");
            $table->foreign("id_dosen")->references("id")->on("dosen")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            //commons
            $table->string("nip", 18)->nullable();

            $table->string("nama");
            $table->string("prefiks")->nullable();
            $table->string("sufiks")->nullable();

            $table->enum("jenis_kelamin", [ "L", "P" ])->nullable();
            $table->string("tempat_lahir")->nullable();
            $table->date("tanggal_lahir")->nullable();
            $table->string("alamat")->nullable();
            $table->enum("agama", ["1","2","3","4","5"])->nullable();

            $table->string("email")->nullable();
            $table->string("telepon")->nullable();
            $table->text("media_sosial")->nullable();
            $table->text("biodata")->nullable();

            $table->text("foto")->nullable();

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
        Schema::dropIfExists('informasi_dosen');
    }
}
