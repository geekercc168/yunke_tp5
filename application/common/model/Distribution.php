<?php

namespace app\common\model;

use think\Model;

class Distribution extends Model
{
    /**
     * 分销分成
     */
    function distribution($uid,$price,$cid,$typeid){
        if(config('isdistributionopen')==1){
            $fatherId=model('distribution')->where(['uid'=>$uid])->value('fatherid');
            $grandfatherId=model('distribution')->where(['uid'=>$fatherId])->value('fatherid');
            $grandgrandfatherId=model('distribution')->where(['uid'=>$grandfatherId])->value('fatherid');
            $distributioone=config('distributioone');
            $distributiontwo=config('distributiontwo');
            $distributionthree=config('distributionthree');
            if(!empty($fatherId)){
                $userInfo=model('user')->where('id',$fatherId)->field('yue')->find();
                db('user')->where(['id'=>$fatherId])->update(['yue'=>$userInfo['yue']+round($distributioone*$price,2)]);
                db('distributionprofit')->insert(['uid'=>$uid,'fid'=>$fatherId,'cid'=>$cid,'typeid'=>$typeid,'profit'=>round($distributioone*$price,2),'addtime'=>date('Y-m-d H:i:s', time())]);
            }
            if(!empty($grandfatherId)){
                $userInfo=model('user')->where('id',$grandfatherId)->field('yue')->find();
                db('user')->where(['id'=>$grandfatherId])->update(['yue'=>$userInfo['yue']+round($distributiontwo*$price,2)]);
                db('distributionprofit')->insert(['uid'=>$uid,'fid'=>$grandfatherId,'cid'=>$cid,'typeid'=>$typeid,'profit'=>round($distributiontwo*$price,2),'addtime'=>date('Y-m-d H:i:s', time())]);
            }
            if(!empty($grandgrandfatherId)){
                $userInfo=model('user')->where('id',$grandgrandfatherId)->field('yue')->find();
                db('user')->where(['id'=>$grandgrandfatherId])->update(['yue'=>$userInfo['yue']+round($distributionthree*$price,2)]);
                db('distributionprofit')->insert(['uid'=>$uid,'fid'=>$grandgrandfatherId,'cid'=>$cid,'typeid'=>$typeid,'profit'=>round($distributionthree*$price,2),'addtime'=>date('Y-m-d H:i:s', time())]);
            }
        }
    }
}