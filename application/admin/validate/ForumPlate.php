<?php

namespace app\admin\validate;

use think\Validate;

class ForumPlate extends Validate
{
    protected $rule = [
        'name' => 'require',
        'description' => 'require',
    ];

    protected $message = [
        'name.require' => '请填写论坛板块名称',
        'description.require' => '请填写论坛板块描述'
    ];
}
