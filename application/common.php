<?php
use alisms\SendSms;
!\think\Config::get('app_debug') && error_reporting(E_ERROR | E_PARSE);

// 应用公共文件

/**
 * 发送短信
 * @param string $mobile 手机号码
 * @param string $template 模板ID
 * @param json $content 发送参数flashsale
 */
function sendSMS($mobile,$templateParam,$templateCode,$config){
    $sms = new SendSms();
    return $sms->send($mobile,$templateParam,$templateCode,$config);
}

/**
 * 发送邮件
 * @param $email
 * @param $title
 * @param $content
 * @param null $config
 * @return bool
 */
function send_email($email, $title, $content, $config = null)
{
    $config = empty($config) ? unserialize(config('email_server')) : $config;
    $mail   = new \PHPMailer\PHPMailer\PHPMailer(true); // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                           // Enable verbose debug output
        $mail->isSMTP();                                // Set mailer to use SMTP
        $mail->Host       = $config['host'];            // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                       // Enable SMTP authentication
        $mail->Username   = $config['username'];        // SMTP username
        $mail->Password   = $config['password'];        // SMTP password
        $mail->SMTPSecure = $config['secure'];          // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = $config['port'];            // TCP port to connect to
        //Recipients
        $mail->setFrom($config['username'], $config['fromname']);
        $mail->addAddress($email);                      // Name is optional
        //Content
        $mail->isHTML(true);                            // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body    = $content;
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 数组转xls格式的excel文件
 * @param $data
 * @param $title
 * 示例数据
 * $data = [
 *     [NULL, 2010, 2011, 2012],
 *     ['Q1', 12, 15, 21],
 *     ['Q2', 56, 73, 86],
 *     ['Q3', 52, 61, 69],
 *     ['Q4', 30, 32, 10],
 * ];
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 * @throws PHPExcel_Writer_Exception
 */
function export_excel($data, $title)
{
    ini_set('max_execution_time', '0');
    $phpexcel = new PHPExcel();
    $properties = $phpexcel->getProperties();
    $properties->setCreator("云课科技");
    $properties->setLastModifiedBy("云课科技");
    $properties->setTitle($title);
    $properties->setSubject('云课科技');
    $properties->setDescription("云课科技");
    $properties->setKeywords("云课科技");
    $properties->setCategory("云课科技");
    $sheet = $phpexcel->getActiveSheet();
    $sheet->fromArray($data);
    $sheet->setTitle('Sheet1');
    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0)) -> setAutoSize(true);
    $phpexcel->setActiveSheetIndex(0);
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $filePath = 'upload/excels/export/'.$title.'.xls';
    $objwriter->save($filePath);
    $response = ['code' => 0,'url' => $filePath];
    return $response;
}
/**
 * 数组转xls格式的excel文件，主要时成绩导出，有合并功能
 * @param $data
 * @param $title
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 * @throws PHPExcel_Writer_Exception
 */
function export_excel_cj($data,$title,$userCount,$secCount){
    ini_set('max_execution_time', '0');
    $phpexcel = new PHPExcel();
    $properties = $phpexcel->getProperties();
    $properties->setCreator("云课科技");//作者是谁 可以不设置
    $properties->setLastModifiedBy("云课科技");//最后一次修改的作者
    $properties->setTitle($title);//设置标题
    $properties->setSubject('测试');//设置主题
    $properties->setDescription("备注");//设置备注
    $properties->setKeywords("关键词");//设置关键词
    $properties->setCategory("类别");//设置类别
    $sheet = $phpexcel->getActiveSheet();
    $sheet->setTitle($title.' 学习记录');
    $sheet->fromArray($data);
    $sheet->setCellValue('A1', $title.' 学习记录');
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getFont()->setName('宋体')->setSize(18)->setBold(true);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    for($i=0;$i<$userCount;$i++){
        $start=$i*$secCount+3;
        $sheet->mergeCells('A'.$start.':A'.($start+$secCount-1));
        $sheet->mergeCells('B'.$start.':B'.($start+$secCount-1));
    }
    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0)) -> setAutoSize(true);
    $sheet->setTitle('Sheet1'); // 设置sheet名称
    $phpexcel->setActiveSheetIndex(0);
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $filePath = 'upload/excels/export/'.$title.'学习记录.xls';
    $objwriter->save($filePath);
    $response = ['code' => 0,'url' => $filePath];
    return $response;
}
/**
 * http请求
 * @param string $url 请求的地址
 * @param array $data 发送的参数
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/**
 * 格式化字节大小(无返回单位)
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes_nom($size) {
    for ($i = 0; $size >= 1024 && $i < 2; $i++) $size /= 1024;
    return round($size, 2) ;
}
/**
 * 格式化时间戳
 * @param  number timestamp      时间戳
 * @return string            格式化后时间
 */
function format_time($timestamp) {
    return  date("Y-m-d H:i:s",$timestamp);
}
/**
 * 返回视频状态
 * @param  string  代码
 * @return string 状态
 */
function video_status($code){
    $status=array('Uploading'=>'上传中', 'UploadSucc'=>'上传完成', 'Transcoding'=>'转码中', 'TranscodeFail'=>'转码失败', 'Checking'=>'审核中', 'Blocked'=>'屏蔽', 'Normal'=>'正常',);
    return $status[$code];
}
/**
 * 返回视频二级分类
 * @param  string  代码
 * @return string 状态
 */
function getSubCateName($CateName){
    $arry=explode("-",$CateName);
    return($arry[1]);
}
/**
 * 毫秒转化为小时
 * @param string $time
 * @return string 分钟
 */

function sec_to_minute($sec){
    $hour = sprintf("%02d", floor($sec/3600));
    $minute =sprintf("%02d", floor(($sec-3600*$hour)/60));
    $second =sprintf("%02d", floor((($sec-3600*$hour)-60*$minute)%60));
    echo $hour.':'.$minute.':'.$second;
}
/**
 * 秒转化为分钟
 * @param string $time
 * @return string 分钟
 */
function sec_to_minute2($sec){
    $sec = round($sec/60);
    if ($sec >= 60){
        $hour = floor($sec/60);
        $min = $sec%60;
        $res = $hour.' 小时 ';
        $min != 0  &&  $res .= $min.' 分';
    }else{
        $res = $sec.' 分钟';
    }
    return $res;
}
/**
 * 把json字符串转数组
 * @param json $p
 * @return array
 */

function json_to_array($p)
{
    if (mb_detect_encoding($p, array('ASCII', 'UTF-8', 'GB2312', 'GBK')) != 'UTF-8') {
        $p = iconv('GBK', 'UTF-8', $p);
    }
    return json_decode($p, true);
}
/**
 * 计算json长度
 * @param json $p
 * @return array
 */
function json_count($json)
{
    $arr=json_to_array($json);
    return count($arr);
}

// 生成唯一订单号
function build_order_no()
{
    return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 获取随机位数字符串
 * @param  integer $len 长度
 * @return string
 */
function rand_code($len =10)
{
    return substr(str_shuffle(str_repeat('ABCDEFGHGKMNPQSTUVWY1234567890', 10)), 0, $len);
}
/**
 * 获取随机位数数字
 * @param  integer $len 长度
 * @return string
 */
function rand_number($len = 6)
{
    return substr(str_shuffle(str_repeat('0123456789', 10)), 0, $len);
}

/**
 * 验证手机号是否正确
 */
function check_mobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    $pat = '/'
        . '^13\d{9}$'
        . '|^14[59]\d{8}$'
        . '|^15[^4]\d{8}$'
        . '|^16[2567]\d{8}$'
        . '|^17[0-8]\d{8}$'
        . '|^18\d{9}$'
        . '|^19[13589]\d{8}$'
        . '/';
    return preg_match($pat, $mobile) ? true : false;
}
/**
 *判断是否是手机端
 */
function isMobile()
{
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    if (isset ($_SERVER['HTTP_VIA']))
    {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}
/**
 * 验证固定电话格式
 * @param string $tel 固定电话
 * @return boolean
 */
function check_tel($tel) {
    $chars = "/^([0-9]{3,4}-)?[0-9]{7,8}$/";
    if (preg_match($chars, $tel)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证邮箱格式
 * @param string $email 邮箱
 * @return boolean
 */
function check_email($email)
{
    $chars = "/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i";
    if (preg_match($chars, $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证QQ号码是否正确
 * @param number $mobile
 */
function check_qq($qq)
{
    if (!is_numeric($qq)) {
        return false;
    }
    return true;
}

/**
 * 验证密码长度
 * @param string $password 需要验证的密码
 * @param int $min 最小长度
 * @param int $max 最大长度
 */
function check_password($password, $min, $max)
{
    if (strlen($password) < $min || strlen($password) > $max) {
        return false;
    }
    return true;
}

/**
 * 是否在微信中
 */
function in_wechat()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
}

/**
 * 配置值解析成数组
 * @param string $value 配置值
 * @return array|string
 */
function parse_attr($value)
{
    if (is_array($value)) {
        return $value;
    }
    $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
    if (strpos($value, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int $pid
 * @param int $level
 * @return array
 */
function list_to_level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $k => $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            unset($array[$k]);
            list_to_level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = 'children', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}

// 驼峰命名法转下划线风格
function to_under_score($str)
{
    $array = array();
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == strtolower($str[$i])) {
            $array[] = $str[$i];
        } else {
            if ($i > 0) {
                $array[] = '_';
            }
            $array[] = strtolower($str[$i]);
        }
    }
    $result = implode('', $array);
    return $result;
}

/**
 * 自动生成新尺寸的图片
 * @param string $filename 文件名
 * @param int $width 新图片宽度
 * @param int $height 新图片高度(如果没有填写高度，把高度等比例缩小)
 * @param int $type 缩略图裁剪类型
 *                    1 => 等比例缩放类型
 *                    2 => 缩放后填充类型
 *                    3 => 居中裁剪类型
 *                    4 => 左上角裁剪类型
 *                    5 => 右下角裁剪类型
 *                    6 => 固定尺寸缩放类型
 * @return string     生成缩略图的路径
 */
function resize($filename, $width, $height = null, $type = 1)
{
    if (!is_file(ROOT_PATH . $filename)) {
        return;
    }
    // 如果没有填写高度，把高度等比例缩小
    if ($height == null) {
        $info = getimagesize(ROOT_PATH . $filename);
        if ($width > $info[0]) {
            // 如果缩小后宽度尺寸大于原图尺寸，使用原图尺寸
            $width  = $info[0];
            $height = $info[1];
        } elseif ($width < $info[0]) {
            $height = floor($info[1] * ($width / $info[0]));
        } elseif ($width == $info[0]) {
            return $filename;
        }
    }
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $old_image = $filename;
    $new_image = mb_substr($filename, 0, mb_strrpos($filename, '.')) . '_' . $width . 'x' . $height . '.' . $extension;
    $new_image = str_replace('image', 'cache', $new_image); // 缩略图存放于cache文件夹
    if (!is_file(ROOT_PATH . $new_image) || filectime(ROOT_PATH . $old_image) > filectime(ROOT_PATH . $new_image)) {
        $path        = '';
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
        foreach ($directories as $directory) {
            $path = $path . '/' . $directory;
            if (!is_dir(ROOT_PATH . $path)) {
                @mkdir(ROOT_PATH . $path, 0777);
            }
        }
        list($width_orig, $height_orig) = getimagesize(ROOT_PATH . $old_image);
        if ($width_orig != $width || $height_orig != $height) {
            $image = \think\Image::open(ROOT_PATH . $old_image);
            switch ($type) {
                case 1:
                    $image->thumb($width, $height, \think\Image::THUMB_SCALING);
                    break;

                case 2:
                    $image->thumb($width, $height, \think\Image::THUMB_FILLED);
                    break;

                case 3:
                    $image->thumb($width, $height, \think\Image::THUMB_CENTER);
                    break;

                case 4:
                    $image->thumb($width, $height, \think\Image::THUMB_NORTHWEST);
                    break;

                case 5:
                    $image->thumb($width, $height, \think\Image::THUMB_SOUTHEAST);
                    break;

                case 5:
                    $image->thumb($width, $height, \think\Image::THUMB_FIXED);
                    break;

                default:
                    $image->thumb($width, $height, \think\Image::THUMB_SCALING);
                    break;
            }
            $image->save(ROOT_PATH . $new_image);
        } else {
            copy(ROOT_PATH . $old_image, ROOT_PATH . $new_image);
        }
    }
    return $new_image;
}

/**
 * AES 128位加密
 */
function ASE_encode($ckey, $chairmansourcepwd){
    $ChairmanPWD =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $ckey, $chairmansourcepwd, MCRYPT_MODE_ECB);
    return bin2hex($ChairmanPWD);

}
function pkcs5_pad ($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}
/**
 * hashids加密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return bool|string
 * @throws Exception
 */
function hashids_encode($id, $salt = '', $min_hash_length = 3)
{
    return (new Hashids\Hashids($salt, $min_hash_length))->encode($id);
}

/**
 * hashids解密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return null
 * @throws Exception
 */
function hashids_decode($id, $salt = '', $min_hash_length = 3)
{
    $id = (new Hashids\Hashids($salt, $min_hash_length))->decode($id);
    if (empty($id)) {
        return null;
    }
    return $id['0'];
}

/**
 * 保存后台用户行为
 * @param string $remark 日志备注
 */
function insert_admin_log($remark)
{
    if (session('?admin_auth')) {
        db('adminLog')->insert([
            'admin_id'    => session('admin_auth.admin_id'),
            'username'    => session('admin_auth.username'),
            'useragent'   => request()->server('HTTP_USER_AGENT'),
            'ip'          => request()->ip(),
            'url'         => request()->url(true),
            'method'      => request()->method(),
            'type'        => request()->type(),
            'param'       => json_encode(request()->param()),
            'remark'      => $remark,
            'create_time' => time(),
        ]);
    }
}

/**
 * 保存前台用户行为
 * @param string $remark 日志备注
 */
function insert_user_log($remark)
{
    if (session('?user_auth')) {
        db('userLog')->insert([
            'user_id'     => session('user_auth.user_id'),
            'username'    => session('user_auth.username'),
            'useragent'   => request()->server('HTTP_USER_AGENT'),
            'ip'          => request()->ip(),
            'url'         => request()->url(true),
            'method'      => request()->method(),
            'type'        => request()->type(),
            'param'       => json_encode(request()->param()),
            'remark'      => $remark,
            'create_time' => time(),
        ]);
    }
}

/**
 * 检测管理员是否登录
 * @return integer 0/管理员ID
 */
function is_admin_login()
{
    $admin = session('admin_auth');
    if (empty($admin)) {
        return 0;
    } else {
        return session('admin_auth_sign') == data_auth_sign($admin) ? $admin['admin_id'] : 0;
    }
}

/**
 * 检测会员是否登录
 * @return integer 0/用户ID
 */
function is_user_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
    }
}
/**
 * 根据用户ID获取是教师还是管理员角色
 * @return varchar 用户名
 */
function getRoleName($id){
    $info=db('auth_group_access')->where('uid',$id)->find();
    if($info['group_id']==1){
        return "管理员";
    }else{
        return "教师";
    }
}
/**
 * 根据用户ID获取用户名
 * @return varchar 用户名
 */
function getUserName($id){
    return model('user')->where(['id'=>$id])->value('nickname')?:model('user')->where(['id'=>$id])->value('username');
}
/**
 * 根据用户ID获取用户身份
 * @return varchar 用户名
 */
function getUserIdentity ($id){
    $is_teacher=  model('user')->where(['id'=>$id])->value('is_teacher');
    if($is_teacher==1){
        return "教师账户";
    }elseif ($vip=model('vip')->where(['uid'=>$id])->where('endtime','>',date("Y-m-d H:i:s"))->find()){
        return "<img src='/static/default/img/vip.png' width='30' height='30'>"."&nbsp;&nbsp;<font color='#e1a500'>VIP会员&nbsp;&nbsp;剩余时间：".getLastTime(strtotime($vip['endtime']))."</font>";
    }else {
        return "学生账户";
    }
}
/**
 * 根据用户ID获取用户真实姓名
 * @return varchar 用户名
 */
function getnickName($id){
    $nickname=model('user')->where(['id'=>$id])->value('nickname')?:model('user')->where(['id'=>$id])->value('nickname');
    $userNmae=model('user')->where(['id'=>$id])->value('nickname')?:model('user')->where(['id'=>$id])->value('username');
    if(empty($nickname)){
        return  $userNmae;
    }else{
        return  $nickname;
    }
}
/**
 * 根据用户名获取用户ID
 * @return varchar 用户名
 */
function getUserIdBuyName($name){
    return model('user')->where(['nickname'=>$name])->value('id');
}
/**
 * 根据用户手机号获取用户ID
 * @return varchar 用户名
 */
function getUserIdBuyTel($tel){
    return model('user')->where(['mobile'=>$tel])->value('id');
}
/**
 * 根据用户ID获取用户手机号
 * @return varchar 手机号
 */
function getUserTel($id){
    return model('user')->where(['id'=>$id])->value('mobile');
}
/**
 *  获取用户班级
 */
function getUserGrade($greadId){
    return model('grade')->where(['id'=>$greadId])->value('name');
}
function getUserGradeBuyUid($uid){
    $greadId=model('user')->where(['id'=>$uid])->value('greadId');
    return getUserGrade($greadId);
}
/**
 *  获取用户学校
 */
function getUserSchool($schoolId){
    return model('school')->where(['id'=>$schoolId])->value('name');
}
function getUserSchoolBuyUid($uid){
    $schoolId=model('user')->where(['id'=>$uid])->value('schoolId');
    return getUserSchool($schoolId);
}
/**
 * 根据教师ID获取教师名
 * @return varchar 用户名
 */
function getTeacherName($id){
    $showname= model('admin')->where(['id'=>$id])->value('showname');
    if(empty($showname)){
        return  model('admin')->where(['id'=>$id])->value('username');
    }else{
        return $showname;
    }
}
function getTeacherIdByUid($id){
    if(empty($id)){
        return '';
    }else{
        return model('user')->where('id',$id)->value('admin_id');
    }
}
function getTeacherBrief($id){
    if($id==0){
        return config('website.defaultteacher');
    }else{
        return model('cooperate')->where(['id'=>$id])->value('brief');
    }
}
/**
 * 获取教师的上传目录ID
 * @param int
 * @return array
 */
function getCategoryId($id)
{
    return model('admin')->where(['id'=>$id])->value('category_id');
}
/**
 *根据课程ID和类型获取教师ID
 */
function getTeachetId($cid,$type){
    if($type==1){
        return model('videoCourse')->where(['id'=>$cid])->value('teacher_id');
    }
    if($type==2){
        return model('liveCourse')->where(['id'=>$cid])->value('teacher_id');
    }
}
/**
 *根据前台用户ID获取申请的教师用户名
 */
function getTeacherNameByUid($id){
    return model('user')->where('uid',$id)->value('username');
}
/**
 *获取教师头像
 */
function getUidFromTid($id){
    return model('admin')->where('id',$id)->value('uid');
}
/**
 *根据后台ID获取管理组ID
 */
function getAdminAuthId($id){
    return model('authGroupAccess')->where('uid',$id)->value('group_id');
}
/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); // 排序
    $code = http_build_query($data); // url编码并生成query字符串
    $sign = sha1($code); // 生成签名
    return $sign;
}

/**
 * 清除系统缓存
 */
function clear_cache($directory = null)
{
    $directory = empty($directory) ? ROOT_PATH . 'runtime/cache/' : $directory;
    if (is_dir($directory) == false) {
        return false;
    }
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir($directory . '/' . $file) ? clear_cache($directory . '/' . $file) : unlink($directory . '/' . $file);
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        rmdir($directory);
    }
}

/**
 * 执行远程POST提交
 */
function post_curl($url,$data,$headers = '',$cookie = '')
{
    if(!$url) return false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
/**
 * 执行远程GET提交
 */
function get_curl($url,$headers=''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL            , $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
    curl_setopt($ch, CURLOPT_ENCODING       , 'gzip,deflate');
    curl_setopt($ch, CURLOPT_TIMEOUT        , 60);
    if($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result =  curl_exec($ch);
    curl_close($ch);
    return $result;
}
/**
 * 执行远程PUT提交
 */
function put_curl($url,$headers='',$data=''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_URL            , $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
    curl_setopt($ch, CURLOPT_ENCODING       , 'gzip,deflate');
    curl_setopt($ch, CURLOPT_TIMEOUT        , 60);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result =  curl_exec($ch);
    curl_close($ch);
    return $result;
}
/**
 * 执行远程PATCH提交
 */
function path_curl($url,$headers='',$data=''){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    if($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $result =  curl_exec($curl);
    curl_close($curl);
    return $result;
}
/**
 * 获取本站域名
 */
function get_domain()
{
    $host=$_SERVER["HTTP_HOST"];
    return $host;
}

/**
 * 返回课时类型
 * @param  string  代码
 * @return string 状态
 */
function section_type($code){
    $type=array('1'=>'视频课程', '2'=>'音频课程', '3'=>'文本课程', '4'=>'考试题');
    return $type[$code];
}
/**
 * 返回课程类型
 * @param  string  代码
 * @return string 状态
 */
function course_type($code){
    $type=array('1'=>'点播课程', '2'=>'直播课程', '3'=>'班级');
    return $type[$code];
}
/**
 * 返回题型类型
 */
function get_pre_type($code)
{
    $type=array('0'=>'请选择类型','1'=>'客观题', '2'=>'主观题');
    return $type[$code];
}

/**
 * 根据题型ID获取题型mark
 */
function get_question_mark($id)
{
    $mark=model('QuestionType')->where('id',$id)->find();
    return $mark['mark'];
}

/**
 * 根据题型ID获取分类名称
 */
function get_category_name($id)
{
    $name=model('CourseCategory')->where('id',$id)->find();
    return $name['category_name'];
}
/**
 * 根据题型ID获取题型ID
 */
function get_question_type_id($id)
{
    return model('questions')->where('id',$id)->value('questiontype');
}
/**
 * 根据题型ID获取题型名称
 */
function get_question_type($id)
{
    $mark=model('QuestionType')->where('id',$id)->find();
    return $mark['type_name'];
}

/**
 * 根据题型ID获取试题
 */
function get_question_info($id,$field)
{
    $question=model('Questions')->where('id',$id)->find();
    return $question[$field];
}

/**
 * 根据题型ID获取试题
 */
function app_get_question_info($id)
{
    $question=model('Questions')->where('id',$id)->find();
    $res['id']=$question['id'];
    $res['question']=$question['question'];
    $res['questionselect']=$question['questionselect'];
    $res['questionanswer']=$question['questionanswer'];
    $res['questiondescribe']=$question['questiondescribe'];
    $res['questionselectnumber']=$question['questionselectnumber'];
    $res['answer']=get_answer($id);
    return $res;
}
/**
 * 检测提交的试题答案是否正确
 */
function check_answer($id,$answer){
    $questionanswer=model('Questions')->where('id',$id)->value('questionanswer');
    if(strip_tags(trim($questionanswer))==strip_tags(trim($answer))){
        return true;
    }else{
        return false;
    }
}
function check_m_answer($id,$answer){
    $questionanswer=strip_tags(trim(model('Questions')->where('id',$id)->value('questionanswer')));
    $answer=strip_tags(trim($answer));
    if($questionanswer==$answer){
        return 1;
    }else{
        $rightNUmm=0;
        $erorNUmm=0;
        $len = mb_strlen($answer, 'utf8');
        $tmp = [];
        for ($i = 0;$i < $len;$i++) {
            $tmp[] = $answer[$i];
        }
        foreach ($tmp as $key => $val){
            if(stristr($questionanswer,$val)){
                $rightNUmm=$rightNUmm+1;
            }else{
                $erorNUmm=$erorNUmm+1;
            }
        }
        if($erorNUmm>0){
            return 3;
        }else{
            return 2;
        }
    }
}
/**
 * 获取非选择题试题答案
 */
function get_answer($id){
    return model('Questions')->where('id',$id)->value('questionanswer');
}
/**
 * 获取选择试题答案
 */
function get_answer_select($id){
    return strip_tags(trim(model('Questions')->where('id',$id)->value('questionanswer')));
}
/**
 * 我的答案去除html标签
 */
function formatmyanswer($str){
    return strip_tags(htmlspecialchars_decode($str));
}
/**
 * 获取试题答案解析
 */
function get_analysis($id){
    return (model('Questions')->where('id',$id)->value('questiondescribe'));
}
function get_SingleSelect($id){
    $num=model('Questions')->where('id',$id)->value('questionselectnumber');
    if($num==2){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">';
    }
    if($num==3){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="C" title="C">';

    }
    if($num==4){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="C" title="C">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="D" title="D">';


    }
    if($num==5){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="C" title="C">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="D" title="D">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="E" title="E">';

    }
    if($num==6){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="C" title="C">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="D" title="D">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="E" title="E">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="F" title="F">';

    }
    if($num==7){
        return '<input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="A" title="A">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="B" title="B">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="C" title="C">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="D" title="D">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="E" title="E">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="F" title="F">
                <input type="radio" lay-filter="answer" radioid='.$id.' name="answer['.$id.']" value="G" title="G">';
    }
}
/**
 * 获取多项选择题的选项个数
 */
function get_multiSelect($id){
    $num=model('Questions')->where('id',$id)->value('questionselectnumber');
    if($num==2){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                 <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">';
    }
    if($num==3){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">
                <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="C" title="C">';
    }
    if($num==4){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">
                <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="C" title="C">
                <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="D" title="D">';
    }
    if($num==5){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="C" title="C">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="D" title="D">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="E" title="E">';
    }
    if($num==6){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="C" title="C">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="D" title="D">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="E" title="E">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="F" title="F">';
    }
    if($num==7){
        return '<input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="A" title="A">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="B" title="B">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="C" title="C">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="D" title="D">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="E" title="E">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="F" title="F">
                  <input type="checkbox" lay-filter="answer" checkbox='.$id.' name="answer['.$id.'][]" value="G" title="G">';
    }

}
/**
 * 获取考试标题
 */
function get_exam_title($id){
    return (model('Exams')->where('id',$id)->value('exam'));
}
/**
 * 获取考试开考时间
 */
function getexamstarttime($id){
    $examsetting= (model('Exams')->where('id',$id)->value('examsetting'));
    $arr=json_to_array($examsetting);
    if(empty($arr['starttime'])){
        return '0';
    }else{
        $now=time();
        $start=strtotime($arr['starttime']);
        $end=strtotime('+'.$arr['examtime'].' minutes',$start);
        if($start>$now){
            return $arr['starttime'];
        }
        if($start<$now and $now<$end){
            return '<font color="#FF5722">考试中...</font>';
        }
        if($now>$end){
            return '考试结束';
        }
    }
}
/**
 * 判断题返回信息
 */
function get_yesOrNo($code)
{
    $type=array('0'=>'错','1'=>'对');
    return $type[$code];
}
function markstatus($code)
{
    $type=array('0'=>'<font color="#2F4056">待批阅</font>','1'=>'<font color="#009688">已批阅</font>');
    return $type[$code];
}
/**
 * 根据管理员ID获取管理员名称
 */
function get_admin_name($id)
{
    $admin=model('Admin')->where('id',$id)->find();
    return $admin['username'];
}

/**
 * 获取知识点下的试题数量
 */
function get_question_num($kid)
{
    return model('Questions')->where('questionknowsid',$kid)->count();
}
/**
 * 获取已做的某个知识点下试题数量
 */
function get_doquestion_num($kid)
{
    $myquestions= db('myquestions')->where(['uid'=>is_user_login(),'pointid'=>$kid])->find();
    return json_count($myquestions['myquestions']);
}
/**
 * 获取试题难度
 */
function get_question_level($code)
{
    $type=array('1'=>'易','2'=>'中', '3'=>'难');
    return $type[$code];
}
/**
 * 考试是否通过
 */
function isExamPass($sid,$cid,$ctype,$eid){
    $examinfo=json_to_array(model('exams')->where('id',$eid)->value('examsetting'));
    $myexam=model('myexam')->where(['uid'=>is_user_login(),'sid'=>$sid,'ctype'=>$ctype,'eid'=>$eid])->find();
    $certificateInfo=db('certificate')->where(['cid'=>$cid,'uid'=>is_user_login(),'eid'=>$eid])->value('imgurl');
    if($myexam['status']==1){
        if($myexam['totalscores']>=$examinfo['passscore']){
            if(!$certificateInfo){
                $html= '<a href="/index/exam/dopackage/id/'.hashids_encode($eid).'" class="fr mr10 status examRes">详情</a>'.
                    '<a href="Javascript:void(0);" class="fr mr10 status">合格</a>';
            }else{
                $html='<a href="/index/exam/dopackage/id/'.hashids_encode($eid).'" class="fr mr10 status examRes">详情</a>'.
                    '<a href="/index/user/mycertificate/cid/'.hashids_encode($cid).'/eid/'.hashids_encode($eid).'" data-width="760px" data-height="580px" class="fr mr10 status ajax-iframe">证书</a>'.
                    '<a href="Javascript:void(0);" class="fr mr10 status">合格</a>';
            }
            return $html;
        }else{
            $html='<a href="/learn/type/'.hashids_encode($ctype).'/id/'.hashids_encode($sid).'" eid='.$eid. ' cid='.$cid. ' sid='.$sid. ' type='.$ctype. ' uid="'.is_user_login().'" class="fr mr10 status redoexam">重考</a>'.
                '<a href="Javascript:void(0);" class="fr mr10 status">不及格</a>';;
            return $html;
        }
    }else{
        return '<a href="Javascript:void(0);" class="fr mr10 status">还未批阅</a>';;
    }
}
function isExamPassApp($sid,$cid,$ctype,$eid,$uid){
    $examinfo=json_to_array(model('exams')->where('id',$eid)->value('examsetting'));
    $myexam=model('myexam')->where(['uid'=>$uid,'sid'=>$sid,'ctype'=>$ctype,'eid'=>$eid])->find();
    $certificateInfo=db('certificate')->where(['cid'=>$cid,'uid'=>$uid,'eid'=>$eid])->value('imgurl');
    if($myexam['status']==1){
        if($myexam['totalscores']>=$examinfo['passscore']){
            if(!$certificateInfo){
                $res['code']=1;//合格无证书
            }else{
                $res['code']=2;//合格有证书
                $res['certificate']=formatUrl('/static/default/certificate/'.$certificateInfo);
            }
        }else{
            $res['code']=3;//不合格
        }
    }else{
        $res['code']=0;//无批阅
    }
    return $res;
}
/**
 * 由试卷ID获取试卷出题人ID
 */
function getExamAuthorid($id){
    $exams=model('exams')->where('id',$id)->find();
    return $exams['examauthorid'];
}
/**
 * 判断授权是否到期
 */
function get_autho_last_time($unixEndTime=0){
    if ($unixEndTime <= time()) {
        return '<font color="red">到期</font>';
    }else{
        return getLastTime($unixEndTime);
    }
}
/**
 * 判断班级是否到期
 */
function get_class_last_time($classinfo,$type,$uid=''){
    $uid=$uid?$uid:is_user_login();
    $is_teacher=$classinfo['headteacher']==$uid? 1:0;
    $addCourseTime=model('userCourse')->where(['cid'=>$classinfo['id'],'type'=>3,'uid'=>$uid])->value('addtime');
    $unixEndTime=strtotime("+".$classinfo['youxiaoqi']."days",strtotime($addCourseTime));
    if($type==1){
        if(getAdminAuthId(is_admin_login())==1 || $is_teacher==1){
            return '管理员权限，无限期';
        }else{
            if(isVip($uid)){
                return 'VIP期限内无期限';
            }else{
                if ($unixEndTime <= time()) {
                    return '<font color="red">到期</font>';
                }else{
                    return getLastTime($unixEndTime);
                }
            }
        }
    }

    if($type==2){
        if(isVip($uid)){
            return false;
        }else{
            if ($unixEndTime <= time() ) {
                return true;
            }else{
                return false;
            }
        }
    }if($type==3){
        if(isVip($uid)){
            return 'VIP期限内无期限';
        }else{
            if ($unixEndTime <= time()) {
                return '到期';
            }else{
                return getLastTime($unixEndTime);
            }
        }
    }
}
/**
 * 判断课程是否到期
 */
function get_course_last_time($courseinfo,$type,$uid=''){
    $uid=$uid?$uid:is_user_login();
    $id=$courseinfo['type'].'-'.$courseinfo['id'];
    $is_teacher=getUidFromTid($courseinfo['teacher_id'])==$uid? 1:0;
    $myclassRoom=model('userCourse')->where(['uid'=>$uid,'type'=>3])->select();
    $addCourseTime=model('userCourse')->where(['cid'=>$courseinfo['id'],'type'=>$courseinfo['type'],'uid'=>$uid])->value('addtime');
    foreach ($myclassRoom as $k=> $value) {
        $cids= model('classroom')->where(['id'=>$myclassRoom[$k]['cid']])->value('cids');
        $cidsArr=(json_to_array($cids));
        if(in_array($id,$cidsArr)){
            $addClassRoomTime[]=$myclassRoom[$k]['addtime'];
        }
    }
    if(!empty($addClassRoomTime)){
        $addCourseTimeInclassRoom=max($addClassRoomTime);
        $addCourseTime=max($addCourseTime,$addCourseTimeInclassRoom);
    }
    $unixEndTime=strtotime("+".$courseinfo['youxiaoqi']."days",strtotime($addCourseTime));
    if($type==1){
        if(getAdminAuthId($uid)==1 || $is_teacher==1){
            return '管理员或教师权限，无限期';
        }else{
            if(isVip($uid)){
                return 'VIP期限内无期限';
            }else{
                if ($unixEndTime <= time()) {
                    return '<font color="red">到期</font>';
                }else{
                    return getLastTime($unixEndTime);
                }
            }
        }
    }
    if($type==3){
        if(isVip($uid)){
            return 'VIP期限内无期限';
        }else{
            if ($unixEndTime <= time()) {
                return '到期';
            }else{
                return getLastTime($unixEndTime);
            }
        }
    }
    if($type==2){
        if(getAdminAuthId(is_admin_login())==1 || isVip($uid) || $is_teacher==1){
            return false;
        }else{
            if ($unixEndTime <= time() ) {
                return true;
            }else{
                return false;
            }
        }
    }
    if($type==4){
        if(isVip($uid)){
            return false;
        }else{
            if ($unixEndTime <= time() ) {
                return true;
            }else{
                return false;
            }
        }
    }
}
/**
 * 给定时间戳获取剩余天数
 */
function getLastTime($unixEndTime=0)
{
    $time = $unixEndTime - time();
    $days = 0;
    if ($time >= 86400) {
        $days = (int)($time / 86400);
        $time = $time % 86400;
    }
    $xiaoshi = 0;
    if ($time >= 3600) {
        $xiaoshi = (int)($time / 3600);
        $time = $time % 3600;
    }
    $fen = (int)($time / 60);
    if($days>3600){
        return '终身授权';
    }else{
        return $days . '天' . $xiaoshi . '时' . $fen . '分';
    }

}
/**
 * 发布时间显示
 */
function get_last_time($time)
{
    $time=strtotime($time);
    $todayLast = strtotime(date('Y-m-d 23:59:59'));
    $agoTimeTrue = time() - $time;
    $agoTime = $todayLast - $time;
    $agoDay = floor($agoTime / 86400);

    if ($agoTimeTrue < 60) {
        $result = '刚刚';
    } elseif ($agoTimeTrue < 3600) {
        $result = (ceil($agoTimeTrue / 60)) . '分钟前';
    } elseif ($agoTimeTrue < 3600 * 12) {
        $result = (ceil($agoTimeTrue / 3600)) . '小时前';
    } elseif ($agoDay == 1) {
        $result = '昨天 ';
    } elseif ($agoDay == 2) {
        $result = '前天 ';
    } else {
        $format = date('Y') != date('Y', $time) ? "Y-m-d" : "m-d";
        $result = date($format, $time);
    }
    return $result;
}

/**
 * 回复数量查询
 */
function replaycount($id){
    return model('forumReply')->where(['tid'=>$id])->count();
}

/**
 * 根据键值删除数组中的元素
 */
function array_remove_by_key($arr, $key){
    if(!array_key_exists($key, $arr)){
        return $arr;
    }
    $keys = array_keys($arr);
    $index = array_search($key, $keys);
    if($index !== FALSE){
        array_splice($arr, $index, 1);
    }
    return $arr;
}
/**
 * api 接口正确输出
 * @param string $data 返回数据
 * @param string $message 提示信息
 */
function json_success($data = '', $message = 'success')
{
    header('Content-Type:application/json; charset=utf-8');
    $result['status'] = 1;
    $result['message'] = $message;
    $result['data'] = empty($data) ? [] : $data;

    exit(json_encode($result));
}

/**
 * api 接口错误输出
 * @param int $status 状态码： -1参数错误（开发提示） -2用户提示（用户输入错误、商品不存在等） -9token过期
 * @param string $message 提示信息
 */
function json_error($message = 'error', $status = -1)
{
    header('Content-Type:application/json; charset=utf-8');
    $result['status'] = $status;
    $result['message'] = $message;
    exit(json_encode($result));
}

/**
 * 获取客户端IP地址
 * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) return $ip[$type];

    if (isset($_SERVER['HTTP_X_REAL_IP'])) {
        //nginx 代理模式下，获取客户端真实IP
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        //客户端的ip
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //浏览当前页面的用户计算机的网关
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } else {
        //浏览当前页面的用户计算机的ip地址
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    //IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];

    return $ip[$type];
}

/**
 * 字符转码
 * @param $data
 * @return array|string
 */
function gbk2utf8($data)
{
    if (is_array($data)) {
        return array_map('gbk2utf8', $data);
    }

    return iconv('gbk', 'utf-8//IGNORE', $data); //IGNORE 会忽略掉不能转化的字符，而默认效果是从第一个非法字符截断。
}
/**
 * 生成二维码
 * @param $data
 * @return array|string
 */
function qrcode($url)
{
    vendor("phpqrcode.phpqrcode");
    $level = 'L';
    $size = 4;
    QRcode::png($url, false, $level, $size);
}
/**
 * 返回订单状态
 * @param $cid,$
 * @return array|string
 */
function getOrderSatatus($code){
    if($code==0){
        return "<font color='red'>未支付</font>";
    }
    if($code==1){
        return "支付成功";
    }
}
/**
 * 返回支付方式
 * @param $cid,$
 * @return array|string
 */
function getPayMethod($code){
    if($code=='wxpay'){
        return "微信支付";
    }
    if($code=='alipay'){
        return "支付宝支付";
    }
    if($code=='yuepay'){
        return "账户余额";
    }
    if($code=='free'){
        return "免费添加";
    }
}
/**
 * 获取课程名称
 * @param $cid,$
 * @return array|string
 */
function getCourseName($id,$type){
    if($type==1){
        return $res=model('videoCourse')->where('id',$id)->value('title')?:'课程不存在或已删除';
    }
    if($type==2){
        return model('liveCourse')->where('id',$id)->value('title')?:'课程不存在或已删除';
    }
    if($type==3){
        return model('classroom')->where('id',$id)->value('title')?:'班级不存在或已删除';
    }
    if($type==4){
        return 'VIP会员购买';
    }
    if($type==5){
        return model('offlineCourse')->where('id',$id)->value('title')?:'班级不存在或已删除';
    }
}

/**
 * 获取课程价格
 * @param $cid
 * @return array|string
 */
function getCoursePrice($id,$type){
    if($type==1){
        return $res=model('videoCourse')->where('id',$id)->value('price');
    }
    if($type==2){
        return model('liveCourse')->where('id',$id)->value('price');
    }
    if($type==3){
        return model('classroom')->where('id',$id)->value('price');
    }

}
/**
 * 获取考试名称
 * @param $cid
 * @return array|string
 */
function getExamCourseName($id,$type,$eid){
    if(empty($id)){
        $examsubjectId=model('exams')->where('id',$eid)->value('examsubject');
        return model('courseCategory')->where('id',$examsubjectId)->value('category_name');
    }else{
        if($type==1){
            return model('videoCourse')->where('id',$id)->value('title');
        }
        if($type==2){
            return model('liveCourse')->where('id',$id)->value('title');
        }
        if($type==3){
            return model('classroom')->where('id',$id)->value('title');
        }
    }


}
/**
 * 获取课时名称
 * @param $cid,$
 * @return array|string
 */
function getSectionName($id,$type){
    if($type==1){
        return model('videoSection')->where('id',$id)->value('title');
    }
    if($type==2){
        return model('liveSection')->where('id',$id)->value('title');
    }
}
/**
 * 获取课时类型
 * @param $cid,$
 * @return array|string
 */
function getSectionType($type){
    if($type==1){
        return '视频';
    }
    if($type==2){
        return '音频';
    }
    if($type==3){
        return '文本';
    }
    if($type==4){
        return '考试';
    }
}
/**
 * 获取课程封面
 * @param $cid,$
 * @return array|string
 */
function getCoursePic($id,$type){
    if($type==1){
        return model('videoCourse')->where('id',$id)->value('picture');
    }
    if($type==2){
        return model('liveCourse')->where('id',$id)->value('picture');
    }
    if($type==3){
        return model('classroom')->where('id',$id)->value('picture');
    }
    if($type==4){
        return model('classroom')->where('id',$id)->value('picture');
    }

}
/**
 * 获取课程章节数量
 * @param $cid,$type
 * @return int
 */
function getCourseNum($cid,$type)
{
    if($type==1){
        $unm= model('videoSection')->where(['csid'=>$cid,'ischapter'=>0])->count();
    }
    if($type==2){
        $unm= model('liveSection')->where(['csid'=>$cid,'ischapter'=>0])->count();
    }
    return $unm==0?1:$unm;

}
/**
 * 获取课程购买人数
 * @param $cid,$type
 * @return int
 */
function getUserNum($cid,$type){
    $num=model('userCourse')->where(['cid'=>$cid,'type'=>$type])->count();
    if($type==1){
        $xuNun=model('videoCourse')->where(['id'=>$cid])->value('xuni_num');
    }
    if($type==2){
        $xuNun=model('liveCourse')->where(['id'=>$cid])->value('xuni_num');
    }
    if($type==3){
        $xuNun=model('classroom')->where(['id'=>$cid])->value('xuni');
    }
    if($type==4){
        $xuNun=model('offlineCourse')->where(['id'=>$cid])->value('xuni_num');
    }
    return $num+$xuNun;
}
/**
 * 获取课程购买真实人数
 * @param $cid,$type,$true
 * @return int
 */
function getTrueUserNum($cid,$type){
    $num=model('userCourse')->where(['cid'=>$cid,'type'=>$type])->count();
    return $num;
}
/**
 * 获取课程的评论数
 * @param $cid,$type,$true
 * @return int
 */
function getCommentsNum($cid,$type){
    $num=model('comment')->where(['cid'=>$cid,'cstype'=>$type])->count();
    return $num;
}
/**
 * 获取已学章节数量
 * @param $cid,$type
 * @return int
 */

function getStuduedNum($cid,$type,$uid=null){
    $uid=$uid?:is_user_login();
    return db('learned')->where(['cid'=>$cid,'type'=>$type,'uid'=>$uid,'status'=>1])->count();
}
/**
 * 获取上次的学习记录
 * @param $cid,$type
 * @return array
 */
function getLastStudy($cid,$type){
    $res=db('learned')->where(['cid'=>$cid,'type'=>$type,'uid'=>is_user_login(),'laststudy'=>['neq','']])->order('addtime','desc')->find();
    if(!empty($res)){
        $res['duration']=db('learned')->where(['cid'=>$cid,'type'=>$type,'uid'=>is_user_login()])->sum('duration');
        if($type==1){
            $res['title']=model('videoSection')->where(['id'=>$res['sid']])->value('title');
        }
        if($type==2){
            $res['title']=model('liveSection')->where(['id'=>$res['sid']])->value('title');
        }
    }
    return $res;
}
/**
 * 课程若是免费，输出"免费"
 * @param int
 * @return array
 */
function isFree($pirce){
    if($pirce==0){
        return "<font color='#15c288'><i class='layui-icon layui-icon-rmb mr10' style='color:#15c288'></i><span style='color:#15c288'>免费</span></font>";
    }else{
        return "<i class='layui-icon layui-icon-rmb mr10'></i>$pirce";
    }
}
/**
 * 课程若是免费，输出"免费",APP专用
 * @param int
 * @return array
 */
function isFreeApp($pirce){
    if($pirce==0){
        return "免费";
    }else{
        return $pirce;
    }
}
/**
 * 课程有效期，为0时，输出"不限"
 * @param int
 * @return array
 */
function youxiaoqi($youxiaoqi){
    if($youxiaoqi==0){
        return '不限';
    }else{
        return $youxiaoqi.'天';
    }
}
function youxiaoqi2($youxiaoqi){
    if($youxiaoqi==0){
        return '不限';
    }else{
        return $youxiaoqi.'天';
    }
}
/**
 * 获取直播类型
 * @param int
 * @return array
 */
function getLIveType ($code){
    if($code==1){
        return "一对一课";
    }
    if($code==2){
        return "普通大班课";
    }
    if($code==3){
        return "小班课普通版";
    }
    if($code==4){
        return "小班课专业版";
    }
}
/**
 * 回放视频状态
 * @param int
 * @return array
 */
function getVideoStatus ($code)
{
    if($code==10){
        return "生成中...";
    }
    if($code==20){
        return "转码中..";
    }
    if($code==30){
        return "<font color='red'>转码失败</font>";
    }
    if($code==100){
        return "<font color='green'>转码成功</font>";
    }
}
/**
 * 提现状态
 */
function get_tixian_status($code){
    if($code==0){
        return "<font color='#009688'>待处理</font>";
    }
    if($code==1){
        return "<font color='green'>已完结</font>";
    }
}
/**
 * 时间戳转时间
 */
function stampTodata($stamp){
    return date("Y-m-d H:i:s",$stamp);
}

/**
 *上传视频在没生成缩略图前默认显示的图像
 */
function defaultVideoPic($pic){
    if(empty($pic)){
        return "/static/default/img/no_image_100x100.jpg";
    }else{
        return $pic;
    }
}
/**
 *会员没设置头像时默认的显示
 */
function defaultAvatar($avatar){
    if(empty($avatar)){
        return "/static/default/img/nan.jpg";
    }else{
        return $avatar;
    }
}

/**
 *根据会员ID获取会员头像
 */
function getAvatar($uid){
    return model('user')->where('id',$uid)->value('avatar');
}
/**
 *根据帖子ID获取板块名称
 */
function getPlateName($id){
    return model('forumPlate')->where('id',$id)->value('name');
}
/**
 *根据帖子ID获取发帖用户ID
 */
function getUserIdByTid($id){
    return model('forumTopic')->where('id',$id)->value('uid');
}
/**
 * 递归无限级分类【先序遍历算】，获取任意节点下所有子孩子
 */
function getMenuTree($arrCat, $parent_id = 0, $level = 0)
{
    static  $arrTree = array(); //使用static代替global
    if(empty($arrCat)) return FALSE;
    $level++;
    foreach($arrCat as $key => $value)
    {
        if($value['pid'] == $parent_id)
        {
            $value[ 'level'] = $level;
            $arrTree[] = $value['id'];
            unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
            getMenuTree($arrCat, $value[ 'id'], $level);
        }
    }
    return $arrTree;
}
/**
 * 根据课程是否已经学习
 * @param $cid,$sid，$type
 * @return array
 */
function isStudyBySid($cid,$sid,$type){
    $learned=db('learned')->where(['cid'=>$cid,'sid'=>$sid,'type'=>$type,'uid'=>is_user_login(),'status'=>1])->find();
    if(empty($learned)){
        return '<i class="fa fa-circle-o  fa-fw ml10"></i>';
    }else{
        return '<i class="fa fa-check-circle-o fa-fw ml10 success-color"></i>';
    }
}
function isStudyBySid2($cid,$sid,$type){
    $learned=db('learned')->where(['cid'=>$cid,'sid'=>$sid,'type'=>$type,'uid'=>is_user_login(),'status'=>1])->find();
    if(empty($learned)){
        return 0;
    }else{
        return 1;
    }
}
function isStudyBySidApp($cid,$sid,$type,$uid){
    $learned=db('learned')->where(['cid'=>$cid,'sid'=>$sid,'type'=>$type,'uid'=>$uid,'status'=>1])->find();
    if(!empty($learned)){
        return true;
    }else{
        return false;
    }
}
/**
 * 根据课时的不同类型返回不同的图标
 * @param $cid,$sid，$type
 * @return array
 */
function getSecIcon($type){
    if($type==1){
        return '<i class="fa fa-film fa-fw "></i>';
    }
    if($type==2){
        return '<i class="fa fa-microphone fa-fw "></i>';
    }
    if($type==3){
        return '<i class="fa fa-image fa-fw "></i>';
    }
    if($type==4){
        return '<i class="fa fa-file-text-o fa-fw "></i>';
    }

}
/**
 * 获取直播状态
 * @param $starttime,$duration
 * @return array
 */
function getLiveStatus($starttime,$duration,$type='pc'){
    $now=time();
    $start=strtotime($starttime);
    $enterTime=strtotime('-5 minutes',$start);
    $end=strtotime('+'.$duration.' minutes',$start);
    if($enterTime>$now){
        return $starttime;
    }
    if($enterTime<$now and $now<$end){
        if($type=='pc'){
            return '<font color="#FF5722">正在直播中...</font>';
        }elseif ($type=='app'){
            return '正在直播中...';
        }

    }
    if($now>$end){
        return '直播已结束，查看回放';
    }
}
/**
 * 直播是否结束
 * @param $starttime,$duration
 * @return array
 */
function isLiveOver($starttime,$duration){
    $now=time();
    $start=strtotime($starttime);
    $end=strtotime('+'.$duration.' minutes',$start);
    if($start<$now and $now<$end){
        return false;
    }
    if($now>$end){
        return true;
    }
}
/**
 * 直播是否开始
 * @param $starttime,$duration
 * @return array
 */
function isLiveStart($starttime){
    $now=time();
    $start=strtotime($starttime);
    $enterTime=strtotime('-5 minutes',$start);
    if($enterTime<$now){
        return true;
    }else{
        return false;
    }
}

/**
 *功能：对字符串进行加密处理
 *参数一：需要加密的内容
 *参数二：密钥
 */
function passport_encrypt($str,$key){ //加密函数
    srand((double)microtime() * 1000000);
    $encrypt_key=md5(rand(0, 32000));
    $ctr=0;
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
        $tmp.=$encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
    }
    return base64_encode(passport_key($tmp,$key));
}

/**
 *功能：对字符串进行解密处理
 *参数一：需要解密的密文
 *参数二：密钥
 */
function passport_decrypt($str,$key){ //解密函数
    $str=passport_key(base64_decode($str),$key);
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $md5=$str[$i];
        $tmp.=$str[++$i] ^ $md5;
    }
    return $tmp;
}

/**
 *辅助函数
 */
function passport_key($str,$encrypt_key){
    $encrypt_key=md5($encrypt_key);
    $ctr=0;
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
        $tmp.=$str[$i] ^ $encrypt_key[$ctr++];
    }
    return $tmp;
}
/**
 *功能：判断是否开启ssl
 */
function is_https() {
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return 'https://';
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return 'https://';
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return 'https://';
    }
    return 'http://';
}
/**
 *功能：返回优惠券的使用状态
 */
function getCouponSatatus($code,$endtime){
    if($start=strtotime($endtime)<time()){
        return "<font color='red'>已过期</font>";
    }else{
        if($code==0){
            return "正常";
        }
        if($code==1){
            return "<font color='red'>被禁用</font>";
        }
    }
}
function getCouponUseSatatus($code){
    if($code==0){
        return "未使用";
    }
    if($code==1){
        return "已经使用";
    }
}
/**
 *功能：返回课程促销状态
 */
function getFlashsaleStatus($start,$end){
    $now=time();
    $start=strtotime($start);
    $end=strtotime($end);
    if($start>$now){
        return '活动未开始';
    }
    if($start<$now and $now<$end){
        return '<font color="#FF5722">活动进行中</font>';
    }
    if($now>$end){
        return '活动已结束';
    }
}
/**
 *功能：课程促销信息
 */
function flashsale($cid='',$type='',$yuanprice,$usetype=1){
    $data=model('marketing')->where(['type'=>'flashsale'])->select();
    $id=$type.'-'.$cid;
    $now=time();
    $is=false;
    foreach ($data as $key => $value) {
        $details=json_decode($data[$key]['details'],true);
        $start=strtotime($details['starttime']);
        $end=strtotime($details['endtime']);
        $cuxiaoPrice=round($yuanprice*$details['rate']/10,1);
        foreach ($details['course'] as $key => $value) {
            if($id==$details['course'][$key]['value'] && ($start<$now and $now<$end)){
                $is=true;
            }
        }
    }
    if($usetype==1 && $is){
        return '促销：<span class=" layui-icon layui-icon-rmb mr10 cuxiao"></span><span class="price mr10"><span>'.$cuxiaoPrice.'</span> </span><span id="remain" class="cuxiao"></span><input id="useType" type="hidden" value="1"><input id="nowTime" type="hidden" value="'.$now.'"><input id="endTime" type="hidden" value="'.$details["endtime"].'">';
    }
    if($usetype==2 && $is){
        return '<span class=" layui-icon layui-icon-rmb mr10 cuxiao"></span><span class="price mr10"><span>'.$cuxiaoPrice.'</span> </span>';
    }
    if($usetype==2 && !$is){
        return '<span class=" layui-icon layui-icon-rmb mr10 cuxiao"></span><span class="price mr10"><span>'.$yuanprice.'</span> </span>';
    }
    if($usetype==3 && $is){
        return $cuxiaoPrice;
    }
    if($usetype==4 && $is){
        return $is;
    }
    if($usetype==5 && $is){
        $res['is']=$is;
        $res['cuxiaoPrice']=$cuxiaoPrice;
        $res['end']=$end;
        $res['start']=$start;
        return $res;
    }else if($usetype==5 && !$is){
        $res['is']=false;
        return $res;
    }
}
/**
 *功能：获取班级班主任姓名
 */
function getHeadteacherName(){
    return '班主任';
}
/**
 *功能：获取班级课程数
 */
function getClassroomCourseNum($id){
    $data=model('classroom')->where(['id'=>$id])->find();
    return json_count($data['cids']);
}
/**
 * 获取班级学员学习总进度
 */
function getAllProgress($classRoomId, $uid)
{
    $classroominfo = model('classroom')->where(['id' => $classRoomId])->find();
    $cids = json_to_array($classroominfo['cids']);
    foreach ($cids as $k => $v) {
        $ids = explode('-', $cids[$k], 2);
        $ids[0] == 1 ? $videoIds[] = $ids[1] : $liveIds[] = $ids[1];
        $ids[0] == 1 ? $videoStudied[$ids[1]] = getStuduedNum($ids[1], 1, $uid) : $liveStudied[$ids[1]] = getStuduedNum($ids[1], 2, $uid);
        $ids[0] == 1 ? $videoCourseNum[$ids[1]] = getCourseNum($ids[1], 1) : $liveCourseNum[$ids[1]] = getCourseNum($ids[1], 2);
        $ids[0] == 1 ? $videoProgress[$ids[1]] = round(100 * getStuduedNum($ids[1], 1, $uid) / getCourseNum($ids[1], 1)) : $liveProgress[$ids[1]] = round(100 * getStuduedNum($ids[1], 2, $uid) / getCourseNum($ids[1], 2));
    }
    if(array_sum($videoCourseNum) + array_sum($liveCourseNum)==0){
        $AllProgress=0;
    }else{
        $AllProgress = round(100 * (array_sum($videoStudied) + array_sum($liveStudied)) / (array_sum($videoCourseNum) + array_sum($liveCourseNum)), 2);
    }
    return $AllProgress;
}

/**
 * 判断是否是VIP会员
 */
function isVip($uid){
    if(model('vip')->where(['uid'=>$uid])->where('endtime','>',date("Y-m-d H:i:s"))->find()){
        return true;
    }else{
        return false;
    }
}
/**
 * 防XSS攻击
 */
function RemoveXSS($val) {
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);
    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                $found = false;
            }
        }
    }
    return $val;
}

//防注入
function abacaAddslashes($var) {
    if (! get_magic_quotes_gpc ()) {
        if (is_array ( $var )) {
            foreach ( $var as $key => $val ) {
                $var [$key] = abacaAddslashes ( $val );
            }
        } else {
            $var = addslashes ( $var );
        }
    }
    return $var;
}
/**
 * 加密解密函数
 */
function authcode($string, $operation = 'DECODE', $key = 'www.yunknet.cn', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :  sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&  substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
/**
 * 获取分销提成
 */
function get_distribution_profit($fid,$uid){
    return round(db('distributionprofit')->where(['uid'=>$uid,'fid'=>$fid])->sum('profit'),2);
}
/**
 * 生成宣传海报
 * @param array  参数,包括图片和文字
 * @param string  $filename 生成海报文件名,不传此参数则不生成文件,直接输出图片
 * @return [type] [description]
 */
function createPoster($config = array() , $filename = "") {
    if (empty($filename)) header("content-type: image/png");
    $imageDefault = array(
        'left' => 0,
        'top' => 0,
        'right' => 0,
        'bottom' => 0,
        'width' => 100,
        'height' => 100,
        'opacity' => 100
    );
    $textDefault = array(
        'text' => '',
        'left' => 0,
        'top' => 0,
        'fontSize' => 32, //字号
        'fontColor' => '255,255,255', //字体颜色
        'angle' => 0,
    );
    $background = $config['background']; //海报最底层得背景
    $backgroundInfo = getimagesize($background);
    $backgroundFun = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
    $background = $backgroundFun($background);
    $backgroundWidth = imagesx($background); //背景宽度
    $backgroundHeight = imagesy($background); //背景高度
    $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
    $color = imagecolorallocate($imageRes, 0, 0, 0);
    imagefill($imageRes, 0, 0, $color);
    imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background) , imagesy($background) , imagesx($background) , imagesy($background));
    if (!empty($config['image'])) {
        foreach ($config['image'] as $key => $val) {
            $val = array_merge($imageDefault, $val);
            $info = getimagesize($val['url']);
            $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
            if ($val['stream']) {
                $info = getimagesizefromstring($val['url']);
                $function = 'imagecreatefromstring';
            }
            $res = $function($val['url']);
            $resWidth = $info[0];
            $resHeight = $info[1];
            $canvas = imagecreatetruecolor($val['width'], $val['height']);
            imagefill($canvas, 0, 0, $color);
            $ext = pathinfo($val['url']);
            if (array_key_exists('extension',$ext)) {
                if ($ext['extension'] == 'gif' || $ext['extension'] == 'png') {
                    imageColorTransparent($canvas, $color); //颜色透明

                }
            }
            imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
            if ($val['left'] < 0) {
                $val['left'] = ceil($backgroundWidth - $val['width']) / 2;
            }
            $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
            imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']); //左，上，右，下，宽度，高度，透明度

        }
    }
    //处理文字
    if (!empty($config['text'])) {
        foreach ($config['text'] as $key => $val) {
            $val = array_merge($textDefault, $val);
            list($R, $G, $B) = explode(',', $val['fontColor']);
            $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
            if ($val['left'] < 0) {
                $fontBox = imagettfbbox($val['fontSize'], 0, $val['fontPath'], $val['text']); //文字水平居中实质
                $val['left'] = ceil(($backgroundWidth - $fontBox[2]) / 2); //计算文字的水平位置

            }
            $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
            imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
        }
    }
    if (!empty($filename)) {
        $res = imagejpeg($imageRes, $filename, 90); //保存到本地
        imagedestroy($imageRes);
        if (!$res) return false;
        return $filename;
    } else {
        header("Content-type:image/png");
        imagejpeg($imageRes);
        imagedestroy($imageRes);
    }
}
function formatUrl($url){
    if(strstr($url,'oss-')){
        return "http:".$url;
    }else if(strstr($url,'http:')){
        return $url;
    }else{
        return is_https().get_domain().$url;
    }
}
function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}
/**
 * 获取替换文章中的图片路径
 * @param string $xstr 内容
 * @param string $oriweb 网址
 * @return string
 *
 */
function replaceimg($xstr){
    $saveimgfile_1 = '/upload/examimg/';
    $dirslsitss = ROOT_PATH.'public'.$saveimgfile_1;
    preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i', $xstr, $match);
    foreach($match[1] as $imgurl){
        $arcurl = $imgurl;
        $img=file_get_contents($arcurl);
        if(!empty($img)) {
            $fileimgname = time()."-".rand(1000,9999).".png";
            $filecachs=$dirslsitss."/".$fileimgname;
            $fanhuistr = file_put_contents( $filecachs, $img );
            $saveimgfile = $saveimgfile_1."/".$fileimgname;
            $xstr=str_replace($imgurl,$saveimgfile,$xstr);
        }
    }
    return $xstr;
}
/**
 * 去除html文件的所有图片
 */
function removeImgHtml($str){
    return preg_replace('~<img(.*?)>~s','',$str);
}
/**
 * 去除html标签除了图片
 */
function cleanhtml($str){
    preg_match_all('/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/',$str,$match);
    foreach($match[0] as $key => $val){
        $html=$html.$val;
    }
    return $html ;
}
/**
 * 生成幻灯片广告链接
 */
function getAdUrl($type,$id){
    switch ($type) {
        case '1':
            $url='/course/'.hashids_encode($id).'/ejR.html';
            break;
        case '2':
            $url='/course/'.hashids_encode($id).'/bk5.html';
            break;
        case '3':
            $url='/classroominfo/'.hashids_encode($id).'.html';
            break;
        case '4':
            $url='/contents/'.hashids_encode($id).'.html';
            break;
        case '5':
            $url='/index/exam/dopackage/id/'.hashids_encode($id).'.html';
            break;
        case '6':
            $url='/page/'.hashids_encode($id).'.html';
            break;
    }
    return $url;

}