<?php

namespace app\admin\validate;

use think\Validate;

class Link extends Validate
{
    protected $rule = [
        'title'    => 'require',
        'href' => 'require',
    ];

    protected $message = [
        'title.require'    => '名称不能为空',
        'href.require' => '链接不能为空',
    ];
}
