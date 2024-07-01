<?php

namespace app\admin\validate;

use think\Validate;

class QuestionType extends Validate
{
    protected $rule = [
        'type_name' => 'require',
        'p_type' => 'require',
        'mark' => 'require',
    ];

    protected $message = [
        'type_name.require' => '题型名称不能为空',
        'p_type.require' => '请选择类型',
        'mark.require' => '请选择标识',
    ];
}
