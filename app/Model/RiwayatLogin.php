<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class RiwayatLogin extends Model
{
	protected $table = "riwayat_login";
	protected $guarded = [];
	protected $casts = ["expired_at" => "datetime"];
	protected $hidden = ["token", "user_id", "user_type"];
	protected $appends = ["active", "active_until"];

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

	function getActiveAttribute(){
		return $this->expired_at->gte(Carbon::now());
	}
	function getActiveUntilAttribute(){
		return $this->expired_at->diffForHumans();
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
