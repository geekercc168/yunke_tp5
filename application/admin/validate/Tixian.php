<?php

namespace app\admin\validate;

use think\Validate;

class Tixian extends Validate
{
    protected $rule = [
        'money' => 'require|token',
        'type'   => 'require',
        'account' => 'require',
    ];

    protected $message = [
        'money.require' => '提现金额不能为空',
        'type.require'   => '收款方式不能为空',
        'account.require'    => '收款账户不能为空',
    ];
}
