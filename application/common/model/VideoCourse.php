<?php

namespace app\common\model;

use think\Model;

class VideoCourse extends Model
{
    public function courseCategory()
    {
        return $this->belongsTo('courseCategory', 'cid', 'id')->bind('category_name');
    }
    public function getSection($courseId){
        $sectionList=model('videoSection')->where(['csid'=>$courseId,'ischapter'=>1])->order('sort_order asc')->select();
        if(!empty($sectionList)){
            foreach ($sectionList as $key => $value) {
                $sectionList[$key]['section']=model('videoSection')->field('document',true)->where(['chapterid'=>$sectionList[$key]['id']])->order('sort_order asc')->select();
            }
            return $sectionList;
        }else{
            $sectionList = model('videoSection')->field('document',true)->where(['csid'=>$courseId])->order('sort_order asc')->select();
            return $sectionList;
        }
    }
}