<?php
namespace app\api\controller;
use app\common\controller\IndexBase;
class Apparticle extends IndexBase
{
    /**
     * 获取文章分类
     */
    function getCategory(){
       $Category= model('category')->field('id,category_name as name')->order('sort_order asc')->select();
       return  json_encode($Category);
    }
    /**
     * 获取文章幻灯片
     */
    function getswiper(){
        $swiper = model('ad')->order('sort_order desc')->where(['category'=>'article','status'=>1])->select();
        foreach ($swiper as $k=> $value) {
            $swiper[$k]['img']=formatUrl($value['image']);
        }
        return  json_encode($swiper);
    }

    /**
     * 获取分类文章
     */
    function getArticle(){
        $list = model('article')->order('sort_order asc,update_time desc')->limit(4)->select();
        foreach ($list as $k=> $value) {
            $list[$k]['image']=formatUrl($list[$k]['image']);
        }
        return (json_encode($list));
    }
    /**
     * 获取热门文章
     */
    function getHotArticle(){
        $tuijian=model('article')->where(['is_top'=>1,'is_hot'=>1,'status'=>1])->limit(4)->order('sort_order asc,update_time desc')->select();
        foreach ($tuijian as $k=> $value) {
            $tuijian[$k]['image']=formatUrl($value['image']);
        }
        return (json_encode($tuijian));
    }
    /**
     * 获取最新文章
     */
    function getNewArticle(){
        $param = $this->request->param();
        $where = [];
        $where['status']=1;
        if (isset($param['cid']) && $param['cid']!=0) {
            $where['cid'] = $param['cid'];
        }
        $new=model('article')->where($where)->order('create_time desc')->paginate($param['pagesize']);
        foreach ($new as $k=> $value) {
            $new[$k]['image']=formatUrl($value['image']);
        }
        return (json_encode($new));
    }
    /**
     * 获取文章内容
     */
    function contents(){
        $param=$this->request->param();
        $contents=model('article')->with('category')->where(['id'=>$param['id']])->find();
        model('article')->where(['id'=>$param['id']])->setInc('view');
        $contents['content']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
            function ($r) {
                $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                return str_replace($r[1], $str, $r[0]);
            },$contents['content']
        );
        $contents['authorFcae']=$param['webUrl'].defaultAvatar(getAvatar($contents['uid']));
        $contents['authorName']=$contents['uid']==-1 ? '管理员' :getUserName($contents['uid']);
        return (json_encode($contents));
    }
    /**
     * 获取单页内容
     */
    function pageDetail(){
        $param=$this->request->param();
        $contents=db('page')->where('id',$param['id'])->find();
        $contents['content']= preg_replace_callback('/<[img|IMG].*?src=[\'| \"](?![http|https])(.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',
            function ($r) {
                $str = 'https://'.$_SERVER['HTTP_HOST'].$r[1];
                return str_replace($r[1], $str, $r[0]);
            },$contents['content']
        );
        return (json_encode($contents));
    }
}