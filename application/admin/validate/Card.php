<?php

namespace app\admin\validate;

use think\Validate;

class Card extends Validate
{
    protected $rule = [
        'number' => 'require',
        'money' => 'require',
    ];

    protected $message = [
        'number.require' => '点卡卡号不能为空',
        'money.require' => '点卡面值金额不能为空',
    ];
}
