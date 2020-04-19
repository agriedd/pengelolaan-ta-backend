<?php

namespace App\Repository;
use App\Model\Admin;
use Illuminate\Support\Facades\{
	Hash,
	Cache,
};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminRepository extends Repository
{
	public static function model(){
		return new Admin;
	}



	/**
	 * @method get
	 * return self chaining method if no id parse, or return admin object
	 * 
	 * @param id
	 * 
	 * @return self | Model\Admin
	 * 
	 */
	public static function get($id = null){
		if($id)
			return Cache::rememberForever("admin_{$id}", function() use($id){
				return self::model()->info()->find($id);
			});
		else
			return new self();
	}



	/**
	 * @method getByUsernameExcept
	 * mengambil data admin dengan username spesifik dan data yang diambil bukan dari id tsb.
	 * berguna untuk mengupdate username admin ketika mengecek username yang tersedia
	 * 
	 * @param username
	 * @param id
	 * 
	 * @return Model\Admin
	 * 
	 */
	public static function getByUsernameExcept($username, $id){
		return self::model()->info()
			->where("admin.username", trim($username))
			->where("admin.id", "<>", $id)
			->first();
	}



	/**
	 * @method getAll
	 * mengambil semua data admin dengan menggunakan beberapa filter
	 * 
	 * @param limit
	 * @param page 
	 * @param filters
	 * 
	 * @deprecated page
	 * @deprecated filters
	 * 
	 * @return Array
	 * 
	 */
	public static function getAll($limit = 10, $page = 1, $filters = []){
		return self::model()
			->info()
			->paginate($limit);
	}


	public static function getWithLastAuth($id, $token = null){
		return self::model()->info()->with([
				"riwayatTerakhir" => function($query) use ($token){
					return $query->where("token", $token);
				}
			])->find($id);
	}

	/**
	 * @method insert
	 * menambahkan sebuah data admin baru
	 * 
	 * @param request
	 * 
	 * @return Model\Admin
	 * 
	 */
	public static function insert($request){
		$request = Collection::make($request->all());
		$request->put( "password", Hash::make($request->get("password")))
			->put( "created_at", Carbon::now() )
			->put( "updated_at", Carbon::now() );

		return self::model()->insertGetId( $request->all() );
	}


	/**
	 * @method update
	 * mengubah sebuah data admin
	 * 
	 * @param request
	 * @param id
	 * 
	 * @return Boolean
	 * 
	 */
	public static function update($request, $id){

		Cache::pull("admin_{$id}");

		$request = Collection::make($request->all());
		$request->put( "updated_at", Carbon::now() );
		
		if($request->has("password"))
			$request->put( "password", Hash::make($request->get("password")));

		return self::model()->find($id)->update( $request->all() );
	}


	/**
	 * @method byUsername
	 * chaining method dari get()
	 * mengambil admin dengan username spesifik
	 * 
	 * @param username
	 * 
	 * @return Model\Admin
	 * 
	 */
	public function byUsername($username){
		return $this->model->info()
			->where("username", $username)
			->first();
	}
}