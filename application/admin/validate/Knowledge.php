<?php

namespace app\admin\validate;

use think\Validate;

class Knowledge extends Validate
{
     protected $rule = [
         'title' => 'require',
     ];

     protected $message = [
         'title.require' => '知识点名称不能为空',
     ];
}
