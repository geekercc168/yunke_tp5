<?php

namespace app\index\validate;

use think\Validate;

class ForumReply extends Validate
{
    protected $rule = [
        'content' => 'require|token',
    ];

    protected $message = [
        'content.require' => '请填写回复内容'
    ];
}