<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class RiwayatLogin extends Model
{
	protected $table = "riwayat_login";
	protected $guarded = [];


	//mutasi

	function user()
	{
		return $this->morphTo();
	}

	//relasi

	//scope
    public function scopeSuccess($query)
    {
    	return $query->where("status", "1");
    }
    public function scopeLastActive($query)
    {
    	return $query->where("expired_at", ">=", DB::raw("NOW()"))->orderBy("expired_at", "DESC");
    }
    
}
