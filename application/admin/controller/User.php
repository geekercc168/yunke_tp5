<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class User extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 学员列表
     */
    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $where['mobile'] = ['like', "%" . $param['key'] . "%"];
            }else{
                $where['username'] = ['like', "%" . $param['key'] . "%"];
            }
        }
        $regfield= model('regfield')->select();
        return $this->fetch('index', ['regfield'=>$regfield,'list' => model('user')->order('id desc')->where($where)->paginate(config('page_number'))]);
    }
    /**
     * 添加学员
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            empty($param['password']) && $this->error('密码不能为空');
            if ($this->insert('user', $param) === true) {
                insert_admin_log('添加了用户');
                $this->success('添加成功', url('admin/user/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $regfield= model('regfield')->select();
        $grade=model('grade')->order('sort_order asc')->select();
        $school=model('school')->order('sort_order asc')->select();
        return $this->fetch('save',['regfield'=>$regfield,'grade'=>$grade,'school'=>$school]);
    }
    /**
     * 编辑学员
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (empty($param['password'])) {
                unset($param['password']);
            }
            if ($this->update('user', $param, input('_verify', true)) === true) {
                insert_admin_log('修改了用户');
                $this->success('修改成功', url('admin/user/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $regfield= model('regfield')->select();
        $grade=model('grade')->order('sort_order asc')->select();
        $school=model('school')->order('sort_order asc')->select();
        $info=model('user')->where('id', input('id'))->find();
        return $this->fetch('save', ['regfield'=>$regfield,'grade'=>$grade,'school'=>$school,'data' => model('user')->where('id', input('id'))->find()]);
    }
    /**
     * 删除学员
     */
    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('user', $this->request->param()) === true) {
                insert_admin_log('删除了用户');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 导出学员
     */
    public function export()
    {
        $data = collection(model('user')->field('id,username,nickname,mobile')->order('id desc')->select())->toArray();
        array_unshift($data, ['ID', '登录名', '显示姓名','手机号']);
        insert_admin_log('导出了学员信息');
        $res=export_excel($data, date('YmdHis') . rand(100,999));
        echo json_encode($res);
    }
    /**
     * 导出学员学习记录
     */
    public function exportLearnRecords()
    {
        $param = $this->request->param();
        $i=0;
        if($param['type']==1){
            $secList=collection(model('videoSection')->field('id,sectype')->order('id asc')->where(['csid'=>$param['cid'],'ischapter'=>0])->select())->toArray();
        }
        if($param['type']==2){
            $secList=collection(model('liveSection')->field('id,sectype')->order('id asc')->where(['csid'=>$param['cid'],'ischapter'=>0])->select())->toArray();
        }
        $user=collection(model('userCourse')->field('uid')->where(['cid'=>$param['cid'],'type'=>$param['type']])->order('uid asc')->select())->toArray();
        foreach ($user as $key => $value) {
            foreach ($secList as $k => $value) {
                $data[$i]['uid']=getUserName($user[$key]['uid']);
                $data[$i]['tel']=getUserTel($user[$key]['uid']);
                $data[$i]['sid']=getSectionName($secList[$k]['id'],$param['type']);
                $data[$i]['type']=$param['type']==1?'录播':'直播';
                $data[$i]['stype']=getSectionType($secList[$k]['sectype']);
                if($secList[$k]['sectype']==4){
                    $score=db('myexam')->where(['cid'=>$param['cid'],'ctype'=>$param['type'],'uid'=>$user[$key]['uid']])->value('totalscores');
                    $data[$i]['status']=empty($score)?'0分':$data[$i]['status'].'分';
                }else{
                    $data[$i]['status']=db('learned')->where(['sid'=>$secList[$k]['id'],'type'=>$param['type'],'uid'=>$user[$key]['uid']])->value('status')==1?'已完成':'未完成';
                }
                $data[$i]['duration']=gmdate('H:i:s', db('learned')->where(['sid'=>$secList[$k]['id'],'type'=>$param['type'],'uid'=>$user[$key]['uid']])->value('duration'));
                $data[$i]['addtime']=db('learned')->where(['sid'=>$secList[$k]['id'],'type'=>$param['type'],'uid'=>$user[$key]['uid']])->value('addtime');
                $i++;
            }
        }
        array_unshift($data,['用户名','手机号','课时', '课程类型', '课时类型','学习结果', '学习时常','学习时间']);
        array_unshift($data,['','','','', '', '','', '']);
        insert_admin_log('导出了用户学习记录');
        $res=export_excel_cj($data, getCourseName($param['cid'],$param['type']),count($user),count($secList));
        echo json_encode($res);
    }
    /**
     * 导出学员
     */
    public function exportstudents()
    {
        $param = $this->request->param();
        $i=0;
        $user=collection(model('userCourse')->field('uid')->where(['cid'=>$param['cid'],'type'=>$param['type']])->order('uid asc')->select())->toArray();
        foreach ($user as $key => $value) {
            $data[$i]['coursename']=getCourseName($param['cid'],$param['type']);
            $data[$i]['name']=getUserName($value['uid']);
            $data[$i]['username']=db('address')->where(['uid'=>$value['uid']])->value('username');
            $data[$i]['mobile']=db('address')->where(['uid'=>$value['uid']])->value('mobile');
            $data[$i]['address']=db('address')->where(['uid'=>$value['uid']])->value('address');
            $i++;
        }
        $title=getCourseName($param['cid'],$param['type']).'-学员信息';
        array_unshift($data,['课程名称','学员姓名','邮寄用户名','邮寄手机号','邮寄地址']);
        $res=export_excel($data,$title);
        echo json_encode($res);
    }
    /**
     * 导入学员
     */
    function import()
    {
        return $this->fetch('import');
    }
    /**
     * 导入学员到数据库
     */
    public function importExcel()
    {
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
                $errorNum=0;
                $successNum=0;
                for ($j = 2; $j <= $allRow; $j++) {
                    $data['username'] = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
                    $data['nickname'] = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
                    $data['mobile']   = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
                    $data['password'] = md5($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
                    $data['yue']      = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();
                    $data['create_time']=time();
                    $cids=$objPHPExcel->getActiveSheet()->getCell("F" . $j)->getValue();
                    if (!$user = model('user')->where(['mobile' => $data['mobile']])->find()) {
                        $uid=model('user')->insertGetId($data);
                        $successNum=$successNum+1;
                    } else {
                        model('user')->where('id',$user['id'])->setField('create_time', time());
                        $uid=$user['id'];
                        $errorNum=$errorNum+1;
                    }
                    if(!empty($cids)){
                        $cidArray=explode("|",$cids);
                        for($index=0;$index<count($cidArray);$index++)
                        {
                            $cid_Arrays=explode("-",$cidArray[$index]);
                            $insertArray[]=['uid'=>$uid,'cid'=>$cid_Arrays[1],'type'=>$cid_Arrays[0],'state'=>1,'addtime'=>date('Y-m-d H:i:s', time())];
                        }
                        model('userCourse')->insertAll($insertArray);
                    }
                }
                if($errorNum>0){
                    $msg='导入成功了'.$successNum.'个会员,账号重复了'.$errorNum.'个';
                }else{
                    $msg='导入成功了'.$successNum.'个会员;';
                }
                return ['code' => 1, 'msg' => $msg];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 学员日志
     */
    public function log()
    {
        return $this->fetch('log', ['list' => model('userLog')->order('create_time desc')->paginate(config('page_number'))]);
    }

    public function truncate()
    {
        if ($this->request->isPost()) {
            db()->query('TRUNCATE ' . config('database.prefix') . 'user_log');
            $this->success('操作成功');
        }
    }
    /**
     * 后台添加教师
     */
    public function addteacher(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if(model('user')->where(['username'=>$param['username']])->find()){
                $this->error('这个登录名有重复的学员登录名');exit();
            }
            if($this->insert('user', $param) === true){
                $param['uid']=$this->insertId;
                if ($this->insert('admin', $param) === true) {
                    model('authGroupAccess')->save(['uid' => $this->insertId, 'group_id' => $param['group_id']]);
                    model('user')->where(['id'=>$param['uid']])->update(['admin_id' => $this->insertId, 'is_teacher' => 1]);
                    insert_admin_log('添加了教师');
                    $this->success('添加成功', url('admin/user/teacherList'));
                } else {
                    $this->error($this->errorMsg);
                }
            }else{
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addteacher', ['authGroup' => model('authGroup')->where('status', 1)->select()]);
    }
    /**
     * 导入教师
     */
    function teacherimport()
    {
        return $this->fetch('teacherimport');
    }
    /**
     * 导入教师到数据库
     */
    public function teacherimportExcel()
    {
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
                $errorNum=0;
                $successNum=0;
                for ($j = 2; $j <= $allRow; $j++) {
                    $data['username'] = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
                    $data['nickname'] = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
                    $data['mobile']   = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
                    $data['password'] = md5($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
                    $data['create_time']=time();
                    if (!$user = model('user')->where(['mobile' => $data['mobile']])->find()) {
                        $uid=model('user')->insertGetId($data);
                    } else {
                        model('user')->where('id',$user['id'])->setField('create_time', time());
                        $uid=$user['id'];
                    }
                    $tdata['username']=$data['username'];
                    $tdata['mobile']=$data['mobile'];
                    $tdata['showname']=$data['nickname'];
                    $tdata['password']=$data['password'];
                    $tdata['create_time']=time();
                    $tdata['uid']=$uid;
                    $tdata['group_id']=2;
                    if ($tid=model('admin')->insertGetId($tdata)) {
                        model('authGroupAccess')->insertGetId(['uid' =>$tid, 'group_id' =>2]);
                        model('user')->where(['id'=>$tdata['uid']])->update(['admin_id' => $tid, 'is_teacher' => 1]);
                        $successNum=$successNum+1;
                    } else {
                        $errorNum=$errorNum+1;
                    }
                }
                if($errorNum>0){
                    $msg='导入成功了'.$successNum.'个教师,失败了'.$errorNum.'个';
                }else{
                    $msg='导入成功了'.$successNum.'个教师;';
                }
                return ['code' => 1, 'msg' => $msg];
            } else {
                $excel_path = null;
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
    /**
     * 后台编辑教师信息
     */
    public function editteacher(){
        $teacherInfo=model('admin')->where('id',input('id'))->find();
        return $this->fetch('editteacher',['teacherInfo'=>$teacherInfo]);
    }
    /**
     * 后台删除教师信息
     */
    function delteacher(){
        if ($this->request->isPost()) {
            $teacherInfo=model('admin')->where('id',input('id'))->find();
            if (model('user')->where('id',$teacherInfo['uid'])->delete()) {
                model('authGroupAccess')->where(['uid' => input('id')])->delete();
                model('admin')->where('id',$teacherInfo['id'])->delete();
                insert_admin_log('删除了教师账户');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    /**
     * 后台批量导出教师
     */
    public function exportteacher(){

    }
    /**
     * 教师列表
     */
    public function teacherList(){
        $param = $this->request->param();
        $where = [];
        if (isset($param['key'])) {
            if(check_mobile($param['key'])){
                $where['mobile'] = ['like', "%" . $param['key'] . "%"];
            }else{
                $where['username'] = ['like', "%" . $param['key'] . "%"];
            }
        }
        $list=model('admin')->where('uid',['>',0])->where($where)->paginate(config('page_number'));
        return $this->fetch('teacherList', ['list' =>$list]);
    }
    /**
     * 默认教师列设置
     */
    function defaultTeacher(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('admin', $param, input('_verify', false)) === true) {
                $this->success('修改成功', url('admin/index/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('defaultTeacher', ['teacherInfo' => model('admin')->where('id', 1)->find()]);
    }
    /**
     * 教师申请审核列表
     */
    public function verifyList(){
        $list=model('cooperate')->order('addtime desc')->paginate(config('page_number'));
        return $this->fetch('teacher', ['list' =>$list]);
    }
    /**
     * 删除教师申请
     */
    public function delverify(){
        if ($this->request->isPost()) {
            if ($this->delete('cooperate', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 教师申请资料详情
     */
    public function details(){
        $param = $this->request->param();
        $details=model('cooperate')->where('id',$param['id'])->find();
        return $this->fetch('details', ['details' =>$details]);
    }
    /**
     * 教师申请审核
     */
    public function verify(){
       if ($this->request->isPost()) {
            $param = $this->request->param();
            if($param['status']==0){
                $this->error('已经审核过了,不要重复操作！',url('admin/user/verifyList'));exit();
            }
            if(empty(config('KeyID')) or empty(config('KeySecret'))){
               $this->error('请先配置云点播参数，以便为教师分配上传目录');
               exit();
             }
            if ($this->update('cooperate', $param, input('_verify', true)) === true) {
                $cooperateInfo=model('cooperate')->where(['id'=>$param['id']])->find();
                $user=model('user')->where(['id'=>$cooperateInfo['uid']])->find();
                if(!model('admin')->where(['cooperateid'=>$cooperateInfo['id']])->find()){
                     $res=controller('admin/educloud')->addCategoryPhpSDK($cooperateInfo['username'],0);
                     if(empty($res['Category']['CateId'])){
                         $this->error('创建视频目录失败');
                     }
                     $id=model('admin')->insertGetId(['password'=>$user['password'],'mobile'=>$cooperateInfo['mobile'],'username'=>$cooperateInfo['username'],'category_id'=>$res['Category']['CateId'],'status'=>1,'cooperateid'=>$cooperateInfo['id'],'uid'=>$cooperateInfo['uid']]);
                     $this->update('user', ['id'=>$cooperateInfo['uid'],'admin_id'=>$id,'is_teacher'=>$param['status'],'category_id'=>$res['Category']['CateId']], input('_verify', true));
                     model('authGroupAccess')->save(['uid' => $id, 'group_id' => 2]);
                     $this->success('操作成功!', url('admin/user/teacher'));
                }else{
                    $this->update('user', ['id'=>$cooperateInfo['uid'],'is_teacher'=>$param['status']], input('_verify', true));
                    $this->success('操作成功！', url('admin/user/teacher'));
                }
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 教师状态设置
     */
    public function teacherstatus(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if($this->update('user', ['id'=>$param['uid'],'is_teacher'=>$param['status']], input('_verify', true)) and $this->update('admin', ['uid'=>$param['uid'],'status'=>$param['status']], input('_verify', true),$field = true, $key = 'uid')){
               $this->success('操作成功！', url('admin/user/teacher'));
            } else{
               $this->error($this->errorMsg);
            }
            $this->success('操作成功', url('admin/user/teacher'));
        }
    }
    /**
     * 教师状态设置
     */
    public function forumadmin(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if($this->update('user', ['id'=>$param['uid'],'forumadmin'=>$param['forumadmin']], input('_verify', true)) and $this->update('admin', ['uid'=>$param['uid'],'forumadmin'=>$param['forumadmin']], input('_verify', true),$field = true, $key = 'uid')){
                $this->success('操作成功！', url('admin/user/teacher'));
            } else{
                $this->error($this->errorMsg);
            }
            $this->success('操作成功', url('admin/user/teacher'));
        }
    }
    /**
     * 教师申请条例
     */
    function ordinance(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('other', $param) === true) {
                $this->success('编辑成功', url('admin/user/ordinance'));
            }
        }
        $ordinance=model('other')->where(['type'=>'ordinance'])->find();
        return $this->fetch('ordinance', ['ordinance' =>$ordinance]);
    }

    /**
     * 教师提现申请
     */
    function tixian(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $param['tid']=getTeacherIdByUid(is_user_login());
            if($param['money']<100){
                $this->error('最低提现金额为100元');
                exit();
            }
            if($param['money']>db('profit')->where('tid', getTeacherIdByUid(is_user_login()))->value('profit')){
                $this->error('可提现金额不足');
                exit();
            }
            if ($this->insert('tixian', $param) === true) {
                if(db('profit')->where('tid', getTeacherIdByUid(is_user_login()))->setDec('profit',$param['money'])){
                    $this->success('提交成功', url('admin/index/index'));
                }else {
                    $this->error($this->errorMsg);
                }
            }else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('tixian');
    }
    /**
     * 后台教师提现管理
     */
    function tixianAdmin(){
        return $this->fetch('tixianadmin', ['list' => model('tixian')->order('id desc')->paginate(config('page_number'))]);
    }

    function tianxianPost(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('tixian', $param, input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/user/tixianAdmin'));
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 导出教师提现数据
     */
    function exporttixian(){
        $data = collection(model('tixian')->field('name,type,account,money,addtime')->where(['status'=>0])->order('id desc')->select())->toArray();
        array_unshift($data, ['用户名', '收款方式', '收款账号','提现金额','申请时间']);
        $res=export_excel($data, date('YmdHis'));
        echo json_encode($res);
    }
    /**
     * 给指定用户增加金钱
     */
    function addmoney(){
        $param = $this->request->param();
        if ($this->request->isPost()) {
            $userInfo=model('user')->where(['id'=>$param['id']])->find();
            $param['yue']=$userInfo['yue']+$param['yue'];
            if ($this->update('user', $param, input('_verify', false)) === true) {
                insert_admin_log('修改了用户');
                $this->success('修改成功', url('admin/user/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('addmoney',['data' => model('user')->where('id', input('id'))->find()]);
    }
    /**
     * 学员注册字段管理
     */
    function regfield(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('regfield', $param, input('_verify', true));
            if ($result === true) {
                clear_cache();
                $this->success('修改成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $regfield= model('regfield')->select();
        $grade=model('grade')->order('sort_order asc')->select();
        $school=model('school')->order('sort_order asc')->select();
        return $this->fetch('regfield',['regfield'=>$regfield,'grade'=>$grade,'school'=>$school]);
    }
    /**
     * 添加年级字段数据
     */
    function gradeAdd(){
        if ($this->request->isPost()) {
            if ($this->insert('grade', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('gradeAdd',['cid'=>input('cid')]);
    }
    /**
     * 字段名称编辑
     */
    function gradeNameEdit(){
        if ($this->request->isPost()) {
            if ($this->update('regfield', $this->request->param()) === true) {
                $this->success('编辑成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('gradeNameEdit',['grade'=>db('regfield')->where('id',1)->find()]);
    }
    /**
     * 编辑年级字段数据
     */
    function gradeEdit(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('grade', $param, input('_verify', true));
            if ($result === true) {
                $this->success('修改成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('grade')->get(input('id'));
        return $this->fetch('gradeAdd', ['data' =>$data]);
    }
    /**
     * 删除年级字段数据
     */
    function gradeDell(){
        if ($this->request->isPost()) {
            if ($this->delete('grade', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
    /**
     * 添加学校字段数据
     */
    function schoolAdd(){
        if ($this->request->isPost()) {
            if ($this->insert('school', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('schoolAdd',['cid'=>input('cid')]);
    }
    /**
     * 编辑学校字段数据
     */
    function schoolEdit(){
        if ($this->request->isPost()) {
            $param = $this->request->param();
            $result = $this->update('school', $param, input('_verify', true));
            if ($result === true) {
                $this->success('修改成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data=model('school')->get(input('id'));
        return $this->fetch('schoolAdd', ['data' =>$data]);
    }
    /**
     * 字段名称编辑
     */
    function schoolNameEdit(){
        if ($this->request->isPost()) {
            if ($this->update('regfield', $this->request->param()) === true) {
                $this->success('编辑成功', url('admin/user/regfield'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('schoolNameEdit',['school'=>db('regfield')->where('id',2)->find()]);
    }
    /**
     * 删除学校字段数据
     */
    function schoolDell(){
        if ($this->request->isPost()) {
            if ($this->delete('school', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

}
