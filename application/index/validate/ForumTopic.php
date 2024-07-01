<?php

namespace app\index\validate;

use think\Validate;

class ForumTopic extends Validate
{
    protected $rule = [
        'captcha'  => 'require|captcha',
    ];

    protected $message = [
        'captcha.captcha'  => '验证码错误',
    ];
}