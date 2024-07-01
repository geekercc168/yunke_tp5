<?php

namespace app\common\model;

use think\Model;

class Questions extends Model
{

    public function courseCategory()
    {
        return $this->belongsTo('courseCategory', 'cid', 'id')->bind('category_name');
    }
}