<?php

namespace App\Controllers\Onadb;

use Jenssegers\Agent\Agent;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\ProductService;
use App\Services\ProductCommentService;
use App\Utils\Pagination;

class HomeController extends BaseClass
{

	/**
	 * 메인페이지
	 * 
	 * @param Request $request
	 * @return array
	 */
	public function index( Request $request )
    {

		$agent = new Agent();

		//모바일, 태블릿, 데스크톱 구분
		if ($agent->isMobile()) {
			$default_per_page = 42;
		} elseif ($agent->isTablet()) {
			$default_per_page = 42;
		} else {
			$default_per_page = 40;
		}

		$requestData = $request->all();

		$page = $requestData['page'] ?? 1;
		$per_page = $requestData['per_page'] ?? $default_per_page;
		$search_value = $requestData['search_value'] ?? '';

		$productService = new ProductService();

		$payload = [
			'kind_code' => 'ONAHOLE',
			'site_show' => 'Y',
			'paging' => true,
			'page' => $page,
            'per_page' => $per_page,
			'show_mode' => 'onadb_main',
			'search_value' => $search_value,
		];

		$productList = $productService->getProductList($payload);

		$pagination = new Pagination(
			$productList['total'],
			$productList['per_page'],
			$productList['current_page'],
			10
		);
		$paginationHtml = $pagination->renderLinks();

		// Pagination 객체를 배열로 변환
		$paginationArray = $pagination->toArray();

		$commentService = new ProductCommentService();
		$recent_comments = $commentService->getRecentComments();

		$data = [
			'search_value' => $search_value,
			'productList' => $productList ?? [],
			'pagination' => $paginationArray,
			'paginationHtml' => $paginationHtml,
			'recent_comments' => $recent_comments,
		];

		$layoutData = [
			'side_layout_show' => 'on',
		];

		return view('onadb.home.index', $data)
			->extends('onadb.layout.layout', $layoutData);

	}

}