<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\AdminServices;

class StaffController extends BaseClass 
{

	/**
	 * 직원 목록 조회
	 * 
	 * @return view
	 */
	public function staffList(Request $request) 
	{

		try{

			$requestData = $request->all();

			$ad_work_status = $requestData['work_status'] ?? '재직중';

			$ad_role = $requestData['ad_role'] ?? null;
			$ad_title = $requestData['ad_title'] ?? null;
			$ad_department = $requestData['ad_department'] ?? null;
			$ad_employee_id = $requestData['ad_employee_id'] ?? null;
			$ad_job_type = $requestData['ad_job_type'] ?? null;
			$ad_birth = $requestData['ad_birth'] ?? null;
			$ad_joining = $requestData['ad_joining'] ?? null;

			$adminServices = new AdminServices();
			$payload = [
				'ad_work_status' => $ad_work_status,
			];

			$adminList = $adminServices->getAdminList($payload);

			$data = [
				'adminList' => $adminList,
				'work_status' => $ad_work_status,
			];

			return view('admin.staff.staff_list', $data);

		} catch (Exception $e) {	
			return view('admin.errors.404', [
				'message' => $e->getMessage(),
			])->response(404);
		}

	}


	/**
	 * 신규 직원등록
	 * 
	 * @return view
	 */
	public function staffReg(Request $request) 
	{
		return view('admin.staff.staff_info');
	}


	/**
	 * 직원 상세 조회
	 * 
	 * @return view
	 */
	public function staffInfo(Request $request) 
	{

		$requestData = $request->all();
		$idx = $requestData['idx'] ?? null;

		$adminServices = new AdminServices();
		$payload = [
			'idx' => $idx,
		];
		$admin = $adminServices->getAdmin($payload);

		$data = [
			'mode' => 'view',
			'admin' => $admin,
		];

		return view('admin.staff.staff_info', $data);
	}


	/**
	 * 직원 저장
	 * 
	 * @return json
	 */
	public function createStaff(Request $request) 
	{
		try{

			$requestData = $request->all();

			$adminServices = new AdminServices();
			$admin = $adminServices->createAdmin($requestData);

			return response()->json([
				'success' => true,
				'message' => '직원 생성 완료',
			]);

		} catch (Throwable $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage(),
			], 400);
		}
	}


	/**
	 * 직원 수정
	 * 
	 * @return json
	 */
	public function updateStaff(Request $request) 
	{

		try{

			$requestData = $request->all();

			$adminServices = new AdminServices();
			$admin = $adminServices->updateAdmin($requestData);
			
			return response()->json([
				'success' => true,
				'message' => '직원 수정 완료',
			]);

		} catch (Throwable $e) {
			return response()->json([
				'success' => false,
				'message' => $e->getMessage(),
			], 400);
		}
	}

}