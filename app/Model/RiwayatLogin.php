<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class RiwayatLogin extends Model
{
	protected $table = "riwayat_login";
	protected $guarded = [];
	protected $hidden = ["token", "user_id", "user_type"];

	/**
	 * relasi morph untuk user
	 * 
	 * @return QueryBuilder
	 * 
	 */
	function user()
	{
		return $this->morphTo();
	}


	/**
	 * scope success untuk mengambil riwayat login dengan status
	 * sukses saja
	 * 
	 * @param query
	 * 
	 * @return QueryBuilder
	 * 
	 */
    public function scopeSuccess($query)
    {
    	return $query->where("status", "1");
    }

	/**
	 * scope last_active untuk mengambil riwayat login yang belum
	 * kadaluarsa
	 * 
	 * @param query
	 * 
	 * @return QueryBuilder
	 * 
	 */
    public function scopeLastActive($query)
    {
    	return $query->where("expired_at", ">=", DB::raw("NOW()"))->orderBy("expired_at", "DESC");
    }

	/**
	 * scope last_active untuk mengambil riwayat login yang belum
	 * kadaluarsa
	 * 
	 * @param query
	 * 
	 * @return QueryBuilder
	 * 
	 */
    public function scopeActive($query)
    {
    	return $query->where("expired_at", ">=", DB::raw("NOW()"));
    }
    
}
