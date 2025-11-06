<?php

namespace App\Controllers\Admin;

use App\Core\BaseClass;
use App\Services\GodoApiService;

class ShowdangController extends BaseClass {

    public function __construct() {
        parent::__construct();
    }

    public function godoHbtiStatisticsIndex() {

        $godoApiService = new GodoApiService();
        $result = $godoApiService->getHbtiStatistics();
        
        $data = [
            'hbtiCount' => $result
        ];

        return $data;

    }

}

