<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InformasiJurusan extends Model
{
    protected $table = "informasi_jurusan";
    protected $guarded = [];

    /**
     * relasi belongsTo Jurusan
     * 
     * @return QueryBuilder
     * 
     */
    public function jurusan(){
    	return $this->belongsTo(Jurusan::class, "id_jurusan");
    }

}
