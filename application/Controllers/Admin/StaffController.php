<?php

namespace App\Controllers\Admin;

use App\Core\BaseClass;

class StaffController extends BaseClass {

	public function staffList() {

		return view('admin.staff.staff_list');

	}
}