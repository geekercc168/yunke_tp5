<?php

namespace app\index\validate;

use think\Validate;

class Cooperate extends Validate
{
    protected $rule = [
        'username'=>'token',
    ];
}