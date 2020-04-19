<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;

use Illuminate\Database\Eloquent\Model;
use App\Repository\InformasiAdminRepository;

class Admin extends Model
{
    use Authenticatable, Authorizable;


    protected $table = "admin";

    protected $hidden = [
    	'password'
    ];

    protected $guarded = [];



    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    //relation
    public function riwayat()
    {
        return $this->morphMany(RiwayatLogin::class, 'user');
    }
    public function riwayatTerakhir()
    {
        return $this->morphOne(RiwayatLogin::class, 'user')->latest()->success()->limit(1);
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
