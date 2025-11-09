<?php

namespace App\Controllers\Onadb;

use App\Core\BaseClass;

class HomeController extends BaseClass
{

	public function index() 
    {

		return view('onadb.home.index')
			->extends('onadb.layout.layout');

	}
}