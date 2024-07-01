<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username'=>'token',
    ];

    protected $message = [
       
    ];
}
