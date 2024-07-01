<?php

namespace app\admin\validate;

use think\Validate;

class Coupon extends Validate
{
    protected $rule = [
        'code' => 'require',
        'rate' => 'require',
    ];

    protected $message = [
        'code.require' => '优惠卡卡号不能为空',
        'rate.require' => '优惠卡折扣不能为空',
    ];
}
