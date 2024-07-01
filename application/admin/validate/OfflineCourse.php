<?php

namespace app\admin\validate;

use think\Validate;

class OfflineCourse extends Validate
{
    protected $rule = [
        'cid'     => 'require',
        'title'   => 'require',
        'picture' => 'require',
        'price'   => 'require',
        'brief'   => 'require',
        'xuni_num'=>'require',
    ];

    protected $message = [
        'cid.require'     => '请选择所属分类',
        'title.require'   => '课程标题不能为空',
        'picture.require' => '课程图片不能为空',
        'price.require'   => '课程价格不能为空',
        'brief.require'   => '课程简介不能为空',
        'xuni_num.require'   => '请填写虚拟学员，不设可以设为0',
    ];
}
