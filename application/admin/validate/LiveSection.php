<?php

namespace app\admin\validate;

use think\Validate;

class LiveSection extends Validate
{
    protected $rule = [
        'title'   => 'require',
    ];

    protected $message = [
        'title.require'   => '章名称不能为空',
    ];
}
