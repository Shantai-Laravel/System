<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsSubjectId extends Model
{
    protected $table = 'goods_subject_id';

    protected $fillable = [
        'p_id', 'alias', 'active', 'deleted', 'level', 'position'
    ];

    public function goodsItemId(){
        return $this->hasOne('App\Models\GoodsItemId', 'goods_subject_id', 'id');
    }
}
