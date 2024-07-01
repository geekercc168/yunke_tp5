<?php

namespace app\admin\validate;

use think\Validate;

class Exams extends Validate
{
    protected $rule = [
        'exam' => 'require',
        'examsubject' => 'require',
        'examtime' => 'require',
        'examscore' => 'require'
    ];

    protected $message = [
        'exam.require' => '请填写试卷名称',
        'examsubject.require' => '请选择科目分类',
        'examtime.require' => '请填写考试时长',
        'examscore.require' => '请填写试卷总分'
    ];
}
