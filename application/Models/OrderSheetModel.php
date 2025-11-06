<?php
namespace App\Models;

use App\Core\BaseModel;

class OrderSheetModel extends BaseModel {

	protected $table = 'ona_order';
	protected $primaryKey = 'oo_idx'; 

	/**
     *아직 작업중
     * @return array
     */
    public function get()
    {
        return $this->queryBuilder
            ->table('v2_agency')
            ->select(['idx', 'name'])
            ->where('active', '=', 'Y')
            ->where('mode', '=', 'land')
            ->orderBy('name', 'ASC')
            ->get();
    }

/*
	//주문서 find
	public function find( $idx, $select=[] ) {

		$agency = $this->queryBuilder
            ->table('ona_order');

		//select
		if( !empty($select) ){
			$agency
				->select($select);
		}

		$results = $agency
            ->find($idx);

		return $results;

	}
*/
}