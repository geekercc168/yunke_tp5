<?php

namespace app\admin\validate;

use think\Validate;

class Nav extends Validate
{
    protected $rule = [
        'name'    => 'require',
        'url' => 'require',
    ];

    protected $message = [
        'name.require'    => '请填写菜单名称',
        'url.require' => '请填写菜单打开的url网址',
    ];
}
