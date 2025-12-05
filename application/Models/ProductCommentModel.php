<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductCommentModel extends BaseModel 
{

	protected $table = 'prd_comment';
	protected $primaryKey = 'pc_idx';  //기본값 idx

    protected $fillable = [
        'pc_kind',
        'pc_pd_idx',
        'pc_user_idx',
        'pc_reg_info',
        'pc_score',
        'pc_score_mode',
        'pc_grade',
        'pc_body',
        'pc_category',
        'pc_reg_date',
        'pc_reg_mode',
        'pc_ip',
    ];

    /**
     * 유저 정보 심플 조회
     * @return \App\Core\BelongsToRelation
     */
    public function userSimple() {
        return $this->belongsTo(UserModel::class, 'pc_user_idx', 'user_idx')
            ->select(['user_idx', 'user_nick', 'user_level']);
    }

    public function productSimple() {
        return $this->belongsTo(ProductModel::class, 'pc_pd_idx', 'CD_IDX')
            ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'CD_IMG2']);
    }

}