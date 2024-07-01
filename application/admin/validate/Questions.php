<?php

namespace app\admin\validate;

use think\Validate;

class Questions extends Validate
{
    protected $rule = [
        'questionchapterid' => 'require',
        'questiontype' => 'require',
        'question' => 'require',
        'questionlevel' => 'require',
    ];

    protected $message = [
        'questionchapterid.require' => '请选择试题科目',
        'questiontype.require' => '请选择试题类型',
        'question.require' => '请填写题干',
        'questionlevel.require' => '请选择试题难度',
    ];
}
