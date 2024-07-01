<?php

namespace app\admin\validate;

use think\Validate;

class Classroom extends Validate
{
    protected $rule = [
        'picture'   => 'require',
        'title' => 'require',
        'price' => 'require',
        'brief' => 'require',
    ];

    protected $message = [
        'picture.require'   => '班级封面不能为空',
        'title.require' => '班级标题不能为空',
        'price.require' => '入班价格不能为空',
        'brief.require' => '班价简介不能为空',
    ];
}
