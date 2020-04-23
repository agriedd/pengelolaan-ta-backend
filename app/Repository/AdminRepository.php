<?php

namespace App\Repository;
use App\Model\Admin;
use Illuminate\Support\Facades\{
	Hash,
	Cache,
	DB,
};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminRepository extends Repository
{
	/**
	 * @todo buat interface yang mewajibkan method ini
	 * 
	 */
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
					$query->where("token", $token);
				}
			])->find($id);
	}

	/**
	 * @method insert
	 * menambahkan sebuah data admin baru
	 * 
	 * @param collection
	 * 
	 * @return Model\Admin
	 * 
	 */
	public static function insert($collection){
		$collection->put( "password", Hash::make($collection->get("password")))
			->put( "created_at", Carbon::now() )
			->put( "updated_at", Carbon::now() );

		if($collection->has("id_jurusan") && empty($collection->get("id_jurusan")))
			$collection->put("id_jurusan", DB::raw("NULL"));

		return self::model()->insertGetId( $collection->all() );
	}


	/**
	 * @method update
	 * mengubah sebuah data admin
	 * 
	 * @param collection
	 * @param id
	 * 
	 * @return Boolean
	 * 
	 */
	public static function update($collection, $id){

		Cache::pull("admin_{$id}");

		$collection->put( "updated_at", Carbon::now() );

		if($collection->has("password"))
			$collection->put( "password", Hash::make($collection->get("password")));

		if($collection->has("id_jurusan") && empty($collection->get("id_jurusan")))
			$collection->put("id_jurusan", DB::raw("NULL"));

		return self::model()->find($id)->update( $collection->all() );
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

	public static function remove($id){
		return self::delete($id);
	}
	public static function delete($id){
		return self::model()->find($id)
			->delete();
	}

	/**
	 * reset password admin jurusan
	 * 
	 * @param int id
	 * 
	 * @return int
	 */
	public static function resetPassword($id){
		$result = self::update(collect( [ "password" => env("DEFAULT_PASSWORD", "password") ] ), $id);
		return $result;
	}
}