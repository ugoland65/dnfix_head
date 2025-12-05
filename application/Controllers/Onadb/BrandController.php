<?php

namespace App\Controllers\Onadb;

use App\Core\BaseClass;
use App\Services\BrandService;

class BrandController extends BaseClass
{

    /**
     * 브랜드 목록
     * 
     * @return array
     */
    public function brandList()
    {

        try {

            $brandService = new BrandService();
            $brandList = $brandService->getOnadbBrandList($payload);

            $data = [
                'brandList' => $brandList ?? [],
            ];

            return view('onadb.brand.brand_list', $data)
                ->extends('onadb.layout.layout');

        } catch (Throwable $e) {

            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }

    }

}