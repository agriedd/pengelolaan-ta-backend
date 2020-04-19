<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformasiAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informasi_admin', function (Blueprint $table) {
            //primary key
            $table->id();

            //foreign key
            $table->unsignedBigInteger("id_admin");
            $table->foreign("id_admin")->references("id")->on("admin")
                ->onDelete("cascade")
                ->onUpdate("cascade");

            //polymorph
            $table->nullableMorphs("user");

            //index
            $table->string("nama")->nullable();
            $table->string("nip", 18)->nullable();

            //commons
            $table->enum("status", [0,1])->default(1);
            $table->enum("level", [0,1])->default(0);

            /**
             * @todo foto
             * 
             * akan dipindahkan ke polymorph
             */
            $table->text("foto")->nullable();

            //addtionals
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
        Schema::dropIfExists('informasi_admin');
    }
}
