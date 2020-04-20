<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InformasiAdmin extends Model
{
	protected $table = "informasi_admin";
	protected $guarded = [
		"status",
		"level"
	];

	public function admin()
	{
		return $this->belongsTo(Admin::class, "id_admin", "id");
	}
}
