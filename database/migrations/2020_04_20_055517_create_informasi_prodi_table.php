<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformasiProdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informasi_prodi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_prodi");
            $table->foreign("id_prodi")->references("id")->on("prodi")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            $table->string("email")->nullable();
            $table->string("telepon")->nullable();
            $table->text("media_sosial")->nullable();
            $table->text("keterangan")->nullable();

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
        Schema::dropIfExists('informasi_prodi');
    }
}
