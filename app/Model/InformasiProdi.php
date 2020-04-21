<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InformasiProdi extends Model
{
	protected $table = "informasi_prodi";
	protected $guarded = [];

	/**
	 * relasi belongsto Prodi
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public function prodi()
	{
		return $this->belongsto(Prodi::class, "id_prodi", "id");
	}
}
