<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Repository\{
    AdminRepository as Admin,
    DosenRepository as Dosen,
};
use Auth;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    const ADMIN      = "admin";
    const MAHASISWA  = "mahasiswa";
    const DOSEN      = "dosen";


    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function get($request = null){

        $request = $request ?? app("request");

        $user = Auth::guard("admin")->user() ??
                Auth::guard("dosen")->user() ??
                Auth::guard("mahasiswa")->user() ?? null;

        if($request->guard === self::ADMIN)
            return Admin::get( $user->id );
        if($request->guard === self::DOSEN)
            return Dosen::get( $user->id );
        // if($user instanceof Model\Mahasiswa)
        //     return Mahasiswa::get( $user->id );
    }

    public static function type(){
        if($user instanceof \App\Model\Admin)
            return self::ADMIN;
        if($user instanceof \App\Model\Dosen)
            return self::DOSEN;
        // if($user instanceof Model\Mahasiswa)
        //     return Mahasiswa::get( $user->id );
    }

    public static function getToken($request){
        return $request->BearerToken() ?? $request->_token;
    }
}
