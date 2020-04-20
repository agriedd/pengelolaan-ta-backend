<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("id_jurusan");
            $table->foreign("id_jurusan")->references("id")->on("jurusan")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            $table->string("kd_prodi")->unique();

            $table->string("nama");
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
        Schema::dropIfExists('prodi');
    }
}
