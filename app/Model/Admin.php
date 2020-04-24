<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Repository\InformasiAdminRepository;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = "admin";
    protected $hidden = [
    	'password'
    ];
    protected $casts = [
        "level" => "boolean",
    ];
    protected $guarded = [];


    /**
     * relasi has Many dengan Informasi Admin
     * 
     * @return QueryBuilder
     * 
     */
    public function informasi()
    {
        return $this->hasMany(InformasiAdmin::class, "id_admin", "id");
    }

    /**
     * relasi morph 1 - many dengan user
     * 
     * @return Query
     * 
     */
    public function riwayat()
    {
        return $this->morphMany(RiwayatLogin::class, 'user');
    }

    /**
     * relasi morph 1 - 1 dengan user, user akan memiliki
     * relasi dengan riwayat login terakhir miliknya
     * 
     * @return Query
     */
    public function riwayatTerakhir()
    {
        return $this->morphOne(RiwayatLogin::class, 'user')
            ->lastActive()
            ->success()
            ->limit(1);
    }

    /**
     * @method scopeInfo
     * mutasi pada model admin:
     * 
     * Admin::info()->get();
     * 
     * jika menggunakan mutasi ini agar lebih spesifik dalam penyebutan kolom
     * contoh:
     * 
     * Admin::info()->where("admin.id", "=", "1")->get();
     * 
     * @param query
     * 
     * @return QueryBuilder
     * 
     */
    public function scopeInfo($query)
    {
    	$informasi_admin = InformasiAdminRepository::getLatestAdminQuery();
    	return $query
    		->select([ "informasi.*", "admin.*" ])
    		->leftJoinSub($informasi_admin, "informasi", function($join){
    			return $join->on("admin.id", "=", "informasi.id_admin");
    		});
    }

}
