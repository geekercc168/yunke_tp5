<?php

namespace app\index\validate;

use think\Validate;

class FandPass extends Validate
{
    protected $rule = [
        'captcha'  => 'require|captcha',
    ];

    protected $message = [
        'captcha.require'  => '请输入验证码',
        'captcha.captcha'  => '验证码错误',
    ];
}
