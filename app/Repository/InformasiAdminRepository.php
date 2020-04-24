<?php

namespace App\Repository;
use App\Model\{
	Admin,
	InformasiAdmin,
};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use DB;
use Illuminate\Support\Facades\{
	Hash,
	Cache,
};

/**
 * 
 * ⚠ status dan level admin mungkin bermasalah
 * 
 * @todo update method update agar mengambil data status dan level terakhir
 * @todo bila request yang dikirim kosong
 * @todo atau pindahkan level dan status di model admin
 * 
 */

class InformasiAdminRepository extends Repository
{
	/**
	 * @method model
	 * membuat sebuah instance baru \Model\InformasiAdmin
	 * 
	 * @return \Model\InformasiAdmin
	 * 
	 */
	public static function model(){
		return new InformasiAdmin;
	}



	/**
	 * @method model
	 * mengambil InformasiAdmin dengan sebuah parameter id
	 * 
	 * @param id
	 * 
	 * @return self | Model\InformasiAdmin
	 * 
	 */
	public static function get($id = null){
		if($id)
			return self::model()->find($id);
		else
			return new self();
	}



	/**
	 * @method getByAdmin
	 * mengambil InformasiAdmin terakhir milik Model\Admin
	 * 
	 * @param admin
	 * 
	 * @return Model\InformasiAdmin
	 * 
	 */
	public static function getByAdmin(Admin $admin){
		return self::model()
			->where("id_admin", $admin->id)
			->latest()
			->first();
	}



	/**
	 * @method getAll
	 * mengambil InformasiAdmin dengan parameter filter
	 * 
	 * @param limit
	 * @param page
	 * @param filters
	 * 
	 * @deprecated filters
	 * 
	 * @return Array
	 * 
	 */
	public static function getAll($limit = 10, $page = 1, $filters = []){
		return self::model()->all();
	}



	/**
	 * @method insert
	 * menambah sebuah informasi admin baru
	 * 
	 * @param request
	 * @param admin
	 * 
	 * @return Boolean
	 * 
	 */
	public static function insert($request, Admin $admin){

		Cache::pull("admin_{$admin->id}");

		$request = Collection::make($request->all());
		$request
			->put( "id_admin", $admin->id )
			->put( "created_at", Carbon::now() )
			->put( "updated_at", Carbon::now() );

		return self::model()->insert( $request->all() );
	}



	/**
	 * @method getLatestAdminQuery
	 * subquery untuk join menggunakan joinSub()
	 * 
	 * 
	 * @return QueryBuilder
	 * 
	 */
	public static function getLatestAdminQuery(){
		return self::model()->groupBy(DB::raw("id_admin DESC"));;
	}
}