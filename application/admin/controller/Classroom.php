<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Classroom extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 班级首页
     */
    function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        if (getAdminAuthId(is_admin_login()) != 1) {
            $map['headteacher'] = is_admin_login();
        }

        $list = model('classroom')->where($where)->where($map)->order('sort_order asc,id desc')->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    /**
     * 添加班级
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['addtime']=date('Y-m-d h:i:s', time());
            $param['headteacher'] = is_admin_login();
            $param['status'] = 1;
            $param['is_top'] = 0;
            $param['views'] = 0;
            $param['type'] = 3;
            if ($this->insert('classroom', $param) === true) {
                insert_admin_log('添加了班级');
                clear_cache();
                $this->success('添加成功', url('admin/classroom/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    /**
     * 编辑班级
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                }
                $result = $this->saveAll('classroom', $data, input('_verify', true));
            } else {
                $result = $this->update('classroom', $param, input('_verify', true));
            }
            if ($result === true) {
                clear_cache();
                $this->success('修改成功', url('admin/classroom/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('classroom')->get(input('id'))]);
    }

    /**
     * 删除班级
     */
    public function del()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->delete('classroom', $param) === true) {
                db('user_course')->where(['cid'=>$param['id'],'type'=>3])->delete();
                db('favourite')->where(['cid'=>$param['id'],'type'=>3])->delete();
                db('order')->where(['cid'=>$param['id'],'ctype'=>3])->delete();
                insert_admin_log('删除了班级');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    /**
     * 获取班级课程
     */
    function courseList()
    {
        $data = model('classroom')->get(input('id'));
        empty($data['cids'])?$data['cids']='[]':$data['cids'];
        $allCourse = controller('admin/flashsale')->getCourse(1);
        return $this->fetch('courseList', ['data' => $data, 'selectCourse' => $data['cids'], 'course' => json_encode($allCourse)]);
    }

    public function editPost()
    {
        $param = $this->request->param();
        foreach ($param['course'] as $key => $value) {
            $cids[] = $param['course'][$key]['value'];
        }
        $data['cids'] = json_encode($cids);
        $data['id'] = $param['id'];
        if ($this->update('classroom', $data, input('_verify', false)) === true) {
            $res = ['code' => 0, 'msg' => '编辑成功'];
        } else {
            $res = ['code' => 1, 'msg' => $this->errorMsg];
        }
        echo json_encode($res);
    }

    /**
     * 获取班级学员列表
     */
    function xueyuanList()
    {
        $param = $this->request->param();
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $fiterId=collection(model('user')->field('id')->where(['mobile'=>$param['key']])->select())->toArray();
                foreach ($fiterId as   $key => $value) {
                    $fiterIds[]=$fiterId[$key]['id'];
                }
                $where['uid'] =array('in',$fiterIds);
            }else{
                $fiterId=collection(model('user')->field('id')->where(['nickname'=>$param['key']])->select())->toArray();
                foreach ($fiterId as   $key => $value) {
                    $fiterIds[]=$fiterId[$key]['id'];
                }
                $where['uid'] = array('in',$fiterIds);
            }
        }
        $where['cid']=$param['cid'];
        $where['type']=$param['type'];
        $list = model('userCourse')->order('id desc')->where($where)->paginate(config('page_number'),false,['query'=>request()->param()]);
        foreach ($list as $key => $value) {
            $list[$key]['progress'] = getAllProgress($param['cid'], $list[$key]['uid']);
        }
        return $this->fetch('xueyuanList', ['list' => $list, 'cid' => $param['cid'],'type'=>$param['type']]);
    }
    /**
     * 批量导出班级学员
     */
    function export()
    {
        $param = $this->request->param();
        $list = model('userCourse')->where(['cid' => $param['cid'], 'type' => 3])->select();
        foreach ($list as $key => $value) {
            $data[$i]['classname']=getCourseName($param['cid'],3);
            $data[$i]['name']=getUserName($value['uid']);
            $data[$i]['username']=db('address')->where(['uid'=>$value['uid']])->value('username');
            $data[$i]['mobile']=db('address')->where(['uid'=>$value['uid']])->value('mobile');
            $data[$i]['address']=db('address')->where(['uid'=>$value['uid']])->value('address');
            $i++;
        }
        $title=getCourseName($param['cid'],3).'-学员信息';
        array_unshift($data,['班级名称','学员姓名','邮寄用户名','邮寄手机号','邮寄地址']);
        export_excel($data,$title);
    }

    /**
     * 批量向班级中导入学员
     */
    function import()
    {
        return $this->fetch('import', ['classroomId' => input('cid')]);
    }
    public function importExcel()
    {
        $param = $this->request->param();
        $classRoomId = $param['classroomId'];
        try {
            $file = request()->file('file');
            $info = $file->validate(['size' => 3145728, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'excels');
            if ($info) {
                vendor('PHPExcel.PHPExcel.Reader.Excel5');
                $fileName = $info->getSaveName();
                $filePath = ROOT_PATH . 'public' . DS . 'upload' . DS . 'excels' . DS . str_replace('\\', '/', $fileName);
                $PHPReader = new \PHPExcel_Reader_Excel5();
                $objPHPExcel = $PHPReader->load($filePath);
                $sheet = $objPHPExcel->getSheet(0);
                $allRow = $sheet->getHighestRow();
                for ($j = 2; $j <= $allRow; $j++) {
                    $data['username'] = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
                    $data['mobile'] = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
                    $data['password'] = md5($objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue());
                    $data['yue'] = $objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue();
                    $data['create_time']=time();
                    if (!$user = model('user')->where(['username' => $data['username'], 'mobile' => $data['mobile']])->find()) {
                        $userIds[] = model('user')->insertGetId($data);
                    } else {
                        $userIds[] = $user['id'];
                    }
                }
                $getIds = controller('index/course')->getClassroomCourseIds($classRoomId);
                $videoIds = $getIds['videoIds'];
                $liveIds = $getIds['liveIds'];
                foreach ($userIds as $key => $value) {
                    $res[]= controller('index/course')->batchAddCourse($userIds[$key], $videoIds, $liveIds,$classRoomId);
                    if (!$data = model('user_course')->where(['uid' => $userIds[$key], 'cid' => $classRoomId, 'type' => 3])->find()){
                        model('user_course')->insertGetId(['cid' => $classRoomId, 'uid' => $userIds[$key], 'type' => 3, 'addtime' => date('Y-m-d h:i:s', time()), 'state' => 1]);
                    } else {
                        model('user_course')->where('id',$data['id'])->setField(['addtime'=>date('Y-m-d h:i:s', time())]);
                    }
                }
                return ['code' => 1, 'msg' => '导入成功', 'url' => '/public/upload/file/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                $excel_path = null;
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    function detach(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result=model('userCourse')->where(['uid'=>$param['uid'],'cid'=>$param['cid'],'type'=>3])->delete();
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
    function addstudents(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['uid' => $v, 'cid'=>$param['cid'],'type'=>3,'state'=>1,'addtime'=>date('Y-m-d h:i:s', time())];
                }
                $result = $this->saveAll('userCourse', $data, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('向班级中添加了学员');
                clear_cache();
                $this->success('添加成功', url('admin/classroom/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $param = $this->request->param();
        $added=collection(model('userCourse')->field('uid')->where(['cid'=>$param['cid'],'type'=>3])->select())->toArray();
        foreach ($added as   $key => $value) {
            $addedIds[]=$added[$key]['uid'];
        }
        $where['id'] = array('not in',$addedIds);
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $where['mobile'] = ['like', "%" . $param['key'] . "%"];
            }else{
                $where['username'] = ['like', "%" . $param['key'] . "%"];
            }
        }
        if (isset($param['school'])) {
            $where['schoolId'] = $param['school'];
        }
        if (isset($param['grade'])) {
            $where['greadId'] = $param['grade'];
        }
        $regfield= model('regfield')->select();
        $grade=model('grade')->order('sort_order asc')->select();
        $school=model('school')->order('sort_order asc')->select();
        $list=model('user')->order('id desc')->where($where)->paginate(config('page_number'),false,['query'=>request()->param()]);
        return $this->fetch('addstudents', ['cid'=>$param['cid'],'regfield'=>$regfield,'grade'=>$grade,'school'=>$school,'list' =>$list]);
    }
    function certificate()
    {
        $param = $this->request->param();
        if ($this->request->isPost()) {
            if (!$param['certificatetitle']) {
                $this->error('请先填写培训内容');
            } else {
                $list = model('userCourse')->order('id desc')->where('cid', $param['cid'])->select();
                $num = 0;
                foreach ($list as $key => $value) {
                    $list[$key]['progress'] = getAllProgress($param['cid'], $list[$key]['uid']);
                    $mycertificate = db('certificate')->where(['cid' => $param['cid'], 'uid' => is_user_login(), 'type' => 3])->find();
                    if ($list[$key]['progress'] == '100%' and !$mycertificate) {
                        if ($this->createcertificate($param['certificatetitle'],$param['organ'], $param['cid'], is_user_login())) {
                            $num = $num + 1;
                        }
                    }
                }
                $this->success('发放了' . $num . '张证书');
            }
        }
        return $this->fetch('certificate', ['cid' => input('cid')]);
    }
    function createcertificate($iscertificatetitle,$organ,$cid,$uid){
            $sentMessage= '恭喜您获得'.$iscertificatetitle.'结业证书';
            $this->sentMessage(0,$uid,0,$sentMessage,5,0);
            $identifier=date('YmdH', time()).mt_rand(1000,9999);
            $nian=date('Y', time());
            $yue=date('n', time());
            $ri=date('j', time());
            $username=getUserName($uid)?:'某某';
            $filename = ROOT_PATH .'public/static/default/certificate/'.$identifier.'.jpg';
            $config = array(
                'image'=>array(
                    array(
                        'url'=>ROOT_PATH .'public/static/default/img/seal.png',
                        'is_yuan'=>false,
                        'stream'=>0,
                        'left'=>320,
                        'top'=>890,
                        'right'=>0,
                        'width'=>280,
                        'height'=>280,
                        'opacity'=>100
                    )
                ),
                'text'=>array(
                    array(
                        'text'=>'编号：'.$identifier,
                        'left'=>1200,
                        'top'=>136,
                        'fontSize'=>28,
                        'fontColor'=>'90,90,90',
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',
                    ),
                    array(
                        'text'=>$username,
                        'left'=>170,
                        'top'=>576,
                        'fontSize'=>32,
                        'fontColor'=>'90,90,90',
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',
                    ),
                    array(
                        'text'=>$iscertificatetitle,
                        'left'=>480,
                        'top'=>700,
                        'fontSize'=>32,
                        'fontColor'=>'90,90,90',
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',
                    ),
                    array(
                        'text'=>$nian,
                        'left'=>1100,
                        'top'=>1016,
                        'fontSize'=>28,
                        'fontColor'=>'90,90,90',
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',
                    ),
                    array(
                        'text'=>$yue,
                        'left'=>1250,
                        'top'=>1016,
                        'fontSize'=>28,
                        'fontColor'=>'90,90,90',
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',
                    ),
                    array(
                        'text'=>$ri,
                        'left'=>1333,      //小于0为小平居中
                        'top'=>1016,
                        'fontSize'=>28,     //字号
                        'fontColor'=>'90,90,90',    //字体颜色
                        'angle'=>0,
                        'fontPath'=>ROOT_PATH.'vendor/topthink/think-captcha/assets/zhttfs/1.ttf',     //字体文件
                    )
                ),
                'background'=>ROOT_PATH .'public/static/default/img/certificate.png'
            );
            $imgurl=basename( createPoster($config,$filename));
            $data['uid']=$uid;
            $data['cid']=$cid;
            $data['eid']=0;
            $data['type']=3;
            $data['certificatetitle']=$iscertificatetitle;
            $data['organ']=$organ;
            $data['imgurl']=$imgurl;
            $data['addtime']=date('Y-m-d H:i:s', time());
            if(db('certificate')->insert($data)){
                return true;
            }
        }

}