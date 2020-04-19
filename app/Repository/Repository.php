<?php

namespace App\Repository;

class Repository
{
	protected $query;
	protected $model;

	public function __construct(){
		$this->model = $this->model();
	}
}