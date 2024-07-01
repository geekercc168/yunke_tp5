SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `edu_ad`;
CREATE TABLE IF NOT EXISTS `edu_ad` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(32) NOT NULL DEFAULT '0' COMMENT '分类',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `url` varchar(255) DEFAULT '' COMMENT '链接',
  `target` varchar(10) DEFAULT '' COMMENT '打开方式',
  `image` varchar(255) DEFAULT '' COMMENT '图片',
  `appimage` varchar(255) DEFAULT NULL,
  `appurl` varchar(30) DEFAULT NULL,
  `jumpype` varchar(5) DEFAULT NULL,
  `sort_order` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='广告' ROW_FORMAT=COMPACT;

INSERT INTO `edu_ad` (`id`, `category`, `name`, `description`, `url`, `target`, `image`, `appimage`, `appurl`, `jumpype`, `sort_order`, `status`) VALUES
(3, 'index', '首页轮播', '', 'https://www.yunknet.vip/course/dy7/ejR.html', '_self', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/241702070_f0fci387.png', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/QQ截图20220213131916.png', NULL, NULL, 0, 1),
(4, 'index', '首页轮播2', '', 'https://www.yunknet.vip/course/dy7/ejR.html', '_self', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/300114413_q9zx74oe.jpg', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/105.png', NULL, NULL, 0, 0),
(5, 'index', '首页轮播', '', 'https://www.yunknet.vip/course/dy7/ejR.html', '_blank', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/341119292_rf304tb4.jpg', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/9.png', NULL, NULL, 0, 0),
(6, 'wenda', '问答系统', '', 'https://www.yunknet.vip/classroominfo/ejR.html', '_blank', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/62c19ea225c61.png', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/62c19ea84ee38.png', '3', '4', 0, 1),
(7, 'article', '高一预科班', '高一预科班', 'https://www.yunknet.vip/classroominfo/ejR.html', '_self', '/upload/file/20220306/34d442c4791c23994c227aef68b3a52d.png', '/upload/file/20220306/34d442c4791c23994c227aef68b3a52d.png', NULL, NULL, 0, 0),
(8, 'index', '首页', '', '', '_self', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/68d96968757554f559e1c965eec9d3d9.png', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/微信图片_20220317215650.png', NULL, NULL, 0, 1),
(9, 'article', '首页', '', '', '_blank', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/62c19ea84ee38.png', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/62c19ea84ee38.png', '7', '1', 0, 1);

DROP TABLE IF EXISTS `edu_address`;
CREATE TABLE IF NOT EXISTS `edu_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `username` varchar(15) NOT NULL,
  `address` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资料邮寄地址';

DROP TABLE IF EXISTS `edu_admin`;
CREATE TABLE IF NOT EXISTS `edu_admin` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(5) DEFAULT '0',
  `mobile` varchar(20) DEFAULT NULL,
  `sex` varchar(5) DEFAULT NULL,
  `category_id` varchar(20) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL COMMENT '管理员用户名',
  `showname` varchar(110) DEFAULT NULL,
  `password` varchar(32) NOT NULL COMMENT '管理员密码',
  `email` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '0禁用/1启动',
  `last_login_time` int(10) UNSIGNED DEFAULT NULL COMMENT '上次登录时间',
  `last_login_ip` varchar(16) DEFAULT NULL COMMENT '上次登录IP',
  `login_count` int(11) UNSIGNED DEFAULT '0' COMMENT '登录次数',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `sign` varchar(50) DEFAULT NULL,
  `brief` varchar(600) DEFAULT NULL,
  `forumadmin` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员' ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `edu_admin_log`;
CREATE TABLE IF NOT EXISTS `edu_admin_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员id',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求链接',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '请求类型',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '资源类型',
  `param` text NOT NULL COMMENT '请求参数',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='管理员日志' ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `edu_agreement`;
CREATE TABLE IF NOT EXISTS `edu_agreement` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `agreement` longtext NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户协议';

INSERT INTO `edu_agreement` (`id`, `type`, `agreement`, `addtime`) VALUES
(1, '《中国移动认证服务条款》', '<p>&nbsp; &nbsp; &nbsp; &nbsp; 欢迎使⽤中国移动认证服务。在使⽤本服务前，请您务必仔细阅读并确保已充分理解并接受本协议的全部内容，尤其是以 粗体标注的内容。如不同意本协议及中国移动随时对其的修改或补充内容，您应当⽴即主动停⽌使⽤本服务。若您确认使⽤本 服务即视为您已经阅读并同意本协议的全部内容，并接受中国移动单⽅对本协议条款随时所做的任何修改和补充，本协议即在 您与中国移动之间产⽣法律效⼒，成为双⽅均具有约束⼒的法律⽂件。<br/>⼀、协议范围及修改声明<br/>1.1 协议适⽤主体范围 本协议是您与中国移动关于使⽤本服务所订⽴的协议。<br/>1.2 协议关系 本协议的内容，包括但不仅限于与本服务、本协议相关的新协议、规则、规范以及中国移动可能不断发布的关于本服务相关协 议、规则、规范等内容，前述内容⼀经予以正式发布，即为本协议不可分割的的组成部分，与其构成统⼀的整体。<br/>1.3 协议修改 中国移动有权在必要时通过在⽹页（dev.10086.cn）上发出公告等合理⽅式通知⽤户修改本服务条款及各单项服务的相关条 款。您在享受各项服务时，应及时查阅了解修改的内容，并⾃觉遵守修改后的服务条款及各单项服务的相关条款。如您继续使⽤本服务，则视为您已同意修改的内容；如您不同意修改内容，则有权停⽌使⽤本服务。<br/>⼆、服务使⽤、变更与中⽌<br/>2.1 服务内容 本服务内容是指⽤户使⽤中国移动提供的基于⼿机号码登录互联⽹应⽤的服务。<br/>2.2 服务形式 您可以通过互联⽹应⽤使⽤本服务。<br/>2.3 服务使⽤ 在使⽤本服务前，互联⽹应⽤（包括中国移动认证站点）的页⾯上均有展⽰本协议，您应当阅读并同意本协议的条款后，再按 页⾯上的提⽰完成注册/登录操作。如不同意本协议，您⽆权使⽤本服务。如您触发注册/登录 操作即表⽰您与中国移动达成协 议，完全接受本协议的全部内容。中国移动给予您⼀项个⼈、不可转让及⾮排他性许可，以使⽤本服务。本条款及本协议未明 ⽰授权的其他⼀切权利仍由中国移动保留，您在使⽤这些权利时需另外取得中国移动的明确书⾯许可。<br/> 2.4 服务变更及终⽌ ⽤户或中国移动可随时根据实际情况终⽌本服务。中国移动有权不经通知，单⽅终⽌向您提供某⼀项或多项服务；您同样有权 不经通知，单⽅终⽌接受移动认证的服务。结束⽤户服务后，您使⽤本服务的权利⽴即终⽌，同时，中国移动不再就本服务对 您承担任何义务。您理解并同意，本服务的变更或终⽌属于中国移动商业决策之内容。您不得因本服务的变更、终⽌⽽要求中 国移动继续提供服务或承担任何形式的赔偿责任。<br/>三、⽤户信息保密制度 尊重⽤户信息的私有性是中国移动的⼀贯制度，中国移动将会采取合理的措施保护⽤户的个⼈信息。<br/>3.1 ⽤户信息范围 本协议所称的⽤户信息是指符合法律、法规及相关规定，并符合下述范围的信息：<br/>（1） ⽤户使⽤本服务时中国移动获取到的信息，包括但不限于： ⽹络⾝份标识，包括但不仅限于注册⼿机号码、注册邮箱、密保邮箱、本机号码； ⽤户服务使⽤数据，包括但不仅限于⼿机设备识别码、国际移动⽤户识别码、媒体访问控制地址（MAC）、设备型号、设备 制造商、⼿机操作系统、屏幕尺⼨、屏幕分辨率、IP地址。<br/>（2） 中国移动从合作单位处合法获取的⽤户个⼈信息；<br/>（3） 其他中国移动通过合法途径获取的⽤户个⼈信息。<br/>3.2 ⽤户信息采集及使⽤ 您理解并同意本服务采集您的信息并应⽤于以下事项，包括但不仅限于：<br/>（1） 读取、上传、记录⽤户个⼈信息数据并进⾏同步、分享和储存以实现部分服务功能；<br/>（2） 收集⽤户个⼈信息数据并统计、分析及研究，以改进移动认证和本服务的质量、性能和技术；<br/>（3） 管理、审查⽤户个⼈信息并进⾏处理；<br/>（4） 改进或提⾼⽤户的使⽤安全性和服务性；<br/>（5） ⽤户同意本协议并使⽤中国移动认证能⼒登录第三⽅互联⽹应⽤的，即视为⽤户作出授权指令，向第三⽅互联⽹应⽤直接或间 接开放部分或全部⽹络⾝份标识数据（具体数据类型见3.1）；<br/>（6） 脱敏信息的商业化应⽤：在符合相关法律法规的前提下，中国移动可能会采取技术⼿段对⽤户的个⼈信息进⾏处理，形成⽆法 识别⽤户⾝份的脱敏信息。在此情况下本服务有权使⽤脱敏信息，对⽤户数据库进⾏分析并予以商业化的利⽤；<br/>（7） 适⽤法律法规规定的其他事项。<br/> 3.3 信息保护制度 中国移动承诺⾮经法定原因或⽤户同意，不向除合作单位以外任何第三⽅公开、透露⽤户个⼈隐私信息，并承诺在采集⽤户信 息过程中采取技术措施和其他必要措施，确保⽤户个⼈信息安全，防⽌⽤户个⼈信息泄露、毁损或丢失。 但下述法定情况下，您的个⼈信息将会部分或全部披露：<br/>（1） 经您同意向您本⼈或其他第三⽅披露；<br/>（2） 法律法规规定或有权机关的指⽰提供您的个⼈信息，或因善意确信这样的作法符合下列任⼀项： 与国家安全、国防安全有关的； 与公共安全、公共卫⽣、重⼤公共利益有关的； 与犯罪侦查、起诉、审判和判决执⾏等有关的； 出于维护信息主体或其他个⼈的⽣命、财产等重⼤合法权益但⼜很难得到⽤户本⼈同意的； 所收集的信息是⽤户⾃⾏向社会公众公开的； 从合法公开披露的信息中收集信息的，如合法的新闻报道、政府信息公开等渠道； 根据您的要求签订合同所必需的； ⽤于维护本服务的安全稳定运⾏所必需的，例如发现、处置产品或服务的故障； 为合法的新闻报道所必需的； 学术研究机构基于公共利益开展统计或学术研究所必要，且对外提供学术研究或描述的结果时，对结果中所包含的信息进⾏去 标识化处理的； 法律法规规定的其他情形。 您理解并同意：您在使⽤本服务前选择或同意公开个⼈隐私信息，或您与中国移动及合作单位之间就⽤户个⼈隐私信息公开或 使⽤另有约定，⽤户应⾃⾏承担因此可能产⽣的任何风险，中国移动对此不予负责。<br/>四、⽤户权利<br/>4.1 ⽤户平台账号、密码和安全性<br/>（1） 您有权选择是否使⽤本服务，如您选择使⽤本服务，您将得到平台账号（通⾏证号、⽤户邮箱或⼿机号码）和密码，并将对您 的平台账号和密码安全负全部责任。<br/>（2） 您有义务负责对您的平台账号和密码保密，并需要采取特定措施保护您的号码安全，包括但不限于妥善保管⼿机号码、通过移 动认证平台下发的短信验证码、平台账号与密码、安装防病毒⽊马软件、定期更改密码等措施。且须对您在该账号和密码下发 ⽣的所有活动（包括但不限于信息披露、发布信息、⽹上点击同意或提交各类规则协议、⽹上续签协议或购买服务等）承担责 任。您理解并同意：如发现任何⼈未经授权使⽤您的⼿机账号和密码，或发⽣违反保密规定的任何其他情况，您会⽴即通知平 台。平台不能也不会对因您未能遵守本款规定⽽发⽣的任何损失负责。您理解平台对您的请求采取⾏动需要合理时间，平台对 在采取⾏动前已经产⽣的后果（包括但不限于您的任何损失）不承担任何责任。<br/>（3） 若密码遗失，您可以登录中国移动认证站点（www.cmpassport.com）根据提⽰重置密码。<br/> 4.2 个⼈账户信息修改 您有权在中国移动认证站点上修改其个⼈账户中各项可修改信息，但信息内容必须遵守法律法规并符合⽹络道德，不得含有任 何侮辱、威胁、淫秽、谩骂等侵害他⼈合法权益的⽂字。<br/>五、⽤户义务 ⽤户不得通过任何⼿段恶意使⽤中国移动认证服务，包括但不仅限于以牟利、炒作等为⽬的的多个账户注册登录。⽤户亦不能 盗⽤其他⽤户账户。 如您违反上述规定，则中国移动有权直接采取⼀切必要措施，包括但不仅限于暂停或查封您的账号，追求您的相关法律责任 等。<br/>六、知识产权 中国移动认证所载述的域名商标、⽂字、视像及声⾳内容、图形及图象等均受有关商标法、著作权法等知识产权法的法律保 护，未经中国移动事先书⾯同意，任何企业或个⼈不得以任何形式复制或传递。<br/> 七、拒绝担保与免责声明<br/>7.1 中国移动对移动认证提供的服务不提供任何明⽰或默⽰的担保或保证，包含但不限于商业适售性、特定⽬的之适⽤性及未 侵害他⼈权利等担保或保证。<br/> 7.2 您理解并同意：基于以下原因⽽造成的，包括但不限于利润、信誉、应⽤、数据损失或其他⽆形损失，中国移动不承担任 何直接的、间接的、附带的、特别、衍⽣性的或惩罚性的赔偿责任（即使中国移动事先已被告知发⽣此种赔偿可能性亦然）：<br/>（1） 本服务⽆法使⽤；<br/>（2） 为替换通过本服务购买或取得之任何商品、数据、信息、服务，或缔结的交易⽽发⽣的成本；<br/>（3） 您的传输数据遭到未获授权的存取或篡改；<br/>（4） 任何第三⽅在本服务中所作之声明或⾏为，或与本服务相关的其他事宜，但本服务条款有明确规定的除外。<br/> 7.3 中国移动对本服务下涉及的境内外基础电信运营商的移动通信⽹络的故障、技术缺陷、覆盖范围限制、不可抗⼒、计算机 病毒、⿊客攻击、⽤户所在位置、⽤户关机或其他⾮本服务技术能⼒范围内的事因等造成的服务中断、内容丢失、出现乱码不 承担责任。<br/> 7.4 中国移动将尽⼒维护本服务的安全性及⽅便性，但对服务过程中出现的信息（包括但不限于⽤户发送的信息）删除或储存 失败不承担任何责任。另外中国移动保留判定⽤户的⾏为是否符合本协议要求的权利，如您违背了本协议内任意条款，中国移 动有权终⽌对您提供本服务。<br/> 7.5 相关第三⽅⽹站或应⽤内的信息、服务等由第三⽅提供。该第三⽅⽹站或应⽤内信息、服务等的合法性、安全性以及真实 性，应由该第三⽅⽹站或应⽤经营者负责。⽤户应审慎地判断该第三⽅⽹站或应⽤内服务的合法性等。您理解并同意：出现如 下事项，您应⾃⾏与该第三⽅⽹站或应⽤的经营者进⾏协商解决，不会因此向中国移动追究任何形式的法律责任：<br/>（1） 第三⽅⽹站或应⽤出现侵害您的利益的⾏为违反相关法律法规或本条款的约定；<br/>（2） 第三⽅⽹站或应⽤使⽤您的⼿机号码信息时产⽣的纠纷；<br/>（3） 您在使⽤第三⽅⽹站或应⽤过程中遭受的损失；<br/>（4） 第三⽅⽹站或应⽤侵害您个⼈利益的其他情况。<br/>7.6 为使⽤本服务，您必须经由为您提供⽹络接⼊服务的第三⽅（主要含互联⽹、WAP⽹、⼿机通信三种），并⾃⾏⽀付可能 产⽣的⽹络通信费⽤。此外，您必须⾃⾏配备及负责与各⽹络连线所需之⼀切必要装备，如计算机、⼿机等装置。您理解并同 意，不会因⽹络通讯费⽤及设备使⽤费⽤向中国移动追究任何形式的法律责任<br/></p>', '2022-04-23 02:34:15'),
(2, '《隐私协议》', '<p>云课网校隐私政策<br/>更新日期：2022年4月23日<br/>生效日期：2022年4月23日<br/>我们深知个人信息对用户的重要性，云课网校将按照法律法规等的要求，采取相应安全保护措施，尽力保护用户(以下简称 “您”)的个人信息安全可控。我们承诺将按照本隐私政策收集、使用 和披露用户信息，本隐私政策适用于云课网校的所有产品及服务。<br/>在使用云课网校各项产品或服务前，请仔细阅读本政策， 在确认充分理解并同意后使用相关产品或服务。一旦您开始使用云课网校的产品或服务，即表示您已充分理解并同意本政策。<br/>一、个人信息定义及范围<br/>1、个人信息:指可单独或者与其他信息结合识别您的身份或者反映您的活动情况的任何信息都<br/>将被视作个人信息，包括(但不限于)，您的姓名、地址、电子邮件地址、生日、电话号码以及信用卡信息、性别、国籍、种族、年龄、语言偏好、您的个人看法、观点或偏好、您发送的私人或保密性质的函件以及他人对您的观点或看法等。<br/>2、个人敏感信息:指一旦泄露、非法提供或滥用可能危人身和财产安全，极易导致个人名誉、<br/>身心健康受到损害或歧视性待遇等的个人信息。包括身份证件号码、手机号码、个人生物识别<br/>信息、银行账号、财产信息、行踪轨迹、交易信息、14 岁以下(含)儿童的个人信息等。<br/>二、我们如何收集和使用您的个人信息<br/>为了向您提供课程服务，我们将根据您使用的服务收集您的如下信 息:<br/>1、成为云课网校用户<br/>在使用云课网校的部分服务前，您需要先成为云课网校的注册用户，以便我们提供服务。注册时需要提供账号使用的手机号码，并且您需要填写您的昵称等基本信息并设置密码。如果您仅需使用浏览、搜索及查看基本服务及介绍，您不需要注册及提供上述信息。成为云课网校用户后，您可以对个人信息进行修改，如昵称、头像、性别、年级等。在您注销云课网校账户后，我们将停止为您提供产品或服务，并根据法律规定删除您的个人信息，或进行匿名处理。使用头像功能时，我们会向您告知我们将收集头像信息的相机权限、存储权限等，并获得您的同意。我们向您获取相机权限以帮助您提供您的用户头像照片；我们向您获取存储权限以完成您调整您的用户头像照片。若您认为该信息属于与业务功能有关的非必要或无关的信息，您可以选择不同意我们收取和使用您的该部分信息，如果您选择不同意，则可以选择停止使用云课网校的产品或服务。<br/>2、为向您提供课程服务，您向平台提供信息，并将这些信息进行关联， 这些功能和信息包括:<br/>(1)支付:您在云课网校平台上支付时，可以选择云课网校合作的第三方支付机构(微信支付、支付宝)所提供的第三方服务。支付功能本身并不收集您的个人信息，但我们需要将您的订单信息及对账信息与这些支付机构共享以确认您的支付指令并完成支付。<br/>(2)上课:您可观看已经购买的课程直播和在有效期内的点播课程。在直播的过程中可以参与老师的教学互动，例如讨论区评论、举手发言、连麦、抢红包 等行为。参与互动需要获取您的麦克风、摄像头权限。<br/>(3)练习或测验:主讲或辅导老师在布置练习或测验后，您可以直接在云课网校 App 上作答，并由老师批改。作答的形式包括选择答案、输入文字和拍照上 传图片、音频、视频。这个过程将会获取您的麦克风、拍照、摄像头权限。<br/>(4)信息发布:您在云课网校平台上主动对产品/服务进行评价或发布其他内 容(如发表评论信息)时，平台将收集您发布的信息，并展示您的昵称、头 像、发布内容、发布日期。<br/>(5)客户服务:当您与平台的客服取得联系时，平台的系统可能会记录您与客服之间的通讯记录，以及使用您的账号信息以便核验身份；当您需要平台提供与您订单相关的客户服务时，平台可能会查询您的相关订单信息以便给予您适当的帮助和处理；<br/>(6)账单管理:为展示您账号的订单信息，平台会收集您在使用平台服务过程 中产生的订单信息用于向您展示及便于您对订单进行管理。您在云课网校生成的订单中，将可能包含您的身份信息、联系信息、支付信息，这些都属于敏感信息，请您谨慎向他人展示或对外提供，如云课网校需对外提供订单信息时，将取得您的授权，并尽到合理商业注意义务对您的信息进行去标识化处理，以在最大化保护您的个人信息同时实现信息可用性。<br/>(9)通知权限：您首次下载安装云课网校APP后，我们会向您发送征求您是否同意我们使用您的云课网校APP的通知权限的弹窗，我们获取您的云课网校APP通知权限以便于您更方便地接收云课网校APP发送的通知消息，您可以在弹窗中选择允许我们获取您的通知权限，也可以选<br/>择不允许我们获取您的通知权限。若您选择允许我们获取您的通知权限，则对于您同意使用的通知权限，我们将仅在向您提供途途课堂通知消息时使用，若您选择不允许我们获取您的通知权限，也不会影响您继续使用云课网校APP的功能。当我们收集并使用这些信息实现上述功能时，我们通过动态弹窗征求您的同意，您可以选择同意或拒绝，当您选择同意即代表您授权我们收集并使用您的信息，当您选择拒绝即代表您未授权我们收集并使用您的信息，我们将不会收集和使用您的信息，也可能无法为您提供上述服务。<br/>3、我们通过不断提升的技术手段加强安装在您的设备的软件的安全能力，以防您的个人信息泄露，为了保障向您提供的服务的安全稳定运营，预防病毒、木马程序和其他恶意程序、网站，我们会收集关于您使用产品、服务以及使用方式的信息并将这些信息进行关联，这些信息<br/>包括:<br/>(1)设备信息：我们可能会根据您在软件安装及使用中授予的具体权限，接收并记录您所使用的设备相关信息(包括 IMEI、Serial、SIM 卡 IMSI 识别码、MAC地址、设备机型、操作系统及版本、客户端版本、设备分辨率、包名、设备设置、进程及必要的应用列表、唯一设备标识符等软硬件特征信息)、设备所在位置相关信息(包括 IP 地址、位置信息、能够提供相关信息的 WLAN 接入点、蓝牙和基站传感器信息)。<br/>(2)日志信息:当您使用我们的服务时，我们可能会自动收集您对我们服务的详细使用情况，作为有关网络日志保存，用于分析解决崩溃问题。日志信息 包括您的登录账号、搜索查询内容、IP 地址、浏览器的类型、电信运营商、网 络环境、使用的语言、访问日期和时间、崩溃<br/>记录、停留时长、发布记录。同时为了收集上述基本的个人设备信息，我们将会申请访问您的设备信息的权 限，征求您的同意，我们收集这些信息是为了向您提供我们基本服务和基础功 能，如您拒绝提供上述权限将可能导致您无法使用我们的产品与服务。<br/>4、例外的情况：<br/>4.1以下情形我们可不响应您提出的查询、更正、删除、撤回授权、注销、获取个人信息副本的请求，包括:<br/>(1)与云课网校履行法律法规规定的义务相关的；<br/>(2)与国家安全、国防安全直接相关的；<br/>(3)与公共安全、公共卫生、重大公共利益直接相关的；<br/>(4)与刑事侦查、起诉、审判和执行判决等直接相关的；<br/>(5)云课网校有充分证据表明个人信息主体存在主观恶意或滥用权利的；<br/>(6)出于维护用户或其他个人的生命、财产等重大合法权益但又很难得到本人授权同意的；<br/>(7)响应用户的请求将导致用户或其他个人、组织的合法权益受到严重损害的；<br/>(8)涉及商业机密的。<br/>4.2 根据相关法律法规规定及国家标准，以下情形中，我们可能会依法共享、转让、公开披露您的个人信息无需征得您的同意：<br/>(1)与云课网校履行法律法规规定的义务相关的；<br/>(2)与国家安全、国防安全直接相关的；<br/>(3)与公共安全、公共卫生、重大公共利益直接相关的；<br/>(4)与刑事侦查、起诉、审判和执行判决等直接相关的；<br/>(5)出于维护用户或其他个人的生命、财产等重大合法权益但又很难得到本人授权同意的；<br/>(7)用户自行向社会公众公开的个人信息；<br/>(8)从合法公开披露的信息中收集个人信息的，如合法的新闻报道、政府信息公开等渠道。<br/>4.3 根据相关法律法规规定及国家标准，以下情形中，我们可能会依法收集、使用您的个人信息无需征得您的同意：<br/>(1)与个人信息控制者履行法律法规规定的义务相关的；<br/>(2)与国家安全、国防安全直接相关的；<br/>(3)与公共安全、公共卫生、重大公共利益直接相关的；<br/>(4)与刑事侦查、起诉、审判和判决执行等直接相关的；<br/>(5)出于维护个人信息主体或其他个人的生命、财产等重大合法权益但又很难得到本人授权同意的；<br/>(6)所涉及的个人信息是个人信息主体自行向社会公众公开的；<br/>(7)根据个人信息主体要求签订和履行合同所必需的；<br/>(8)从合法公开披露的信息中收集个人信息的，如合法的新闻报道、政府信息公开等渠道；<br/>(9)维护所提供产品或服务的安全稳定运行所必需的，如发现、处置产品或服务的故障；<br/>(10)个人信息控制者为新闻单位，且其开展合法的新闻报道所必需的；<br/>(11)个人信息控制者为学术研究机构，出于公共利益开展统计或学术研究所必要，且其对外提供学术研究或描述的结果时，对结果中所包含的个人信息进行去标识化处理的。<br/>三、我们如何共享您的个人信息<br/>您同意我们依据本政策与下列信息接收方共享您的个人信息(合称为“信息接收方”)：我们的关联方或相关法人团体；与我们合作的一些商业合作伙伴。我们可能委托商业合作伙伴为您提供某些服务或者代表我们履行职能(简称“我们信任的商业合作伙伴”)。我们仅会出于合法、正当、必要、特定、明确的目的在有限的程度内与其共享您的个人信息，即我们信任的商业合作伙伴为了提供其受聘所需服务而合理所必需的个人信息。我们信任的商业合作伙伴无权将共享的个人信息用于任何其他用途。目前我们信任的商业合作伙伴包括：服务提供商、供应商和其他合作伙伴。例如帮助我们运营网站，代表我们处理与您的个人信息相关的某些活动并向我们的客户和访客提供重要服务的服<br/>务提供商等。除以上信息接收方外，在获取您明确同意情况下，我们可能与其他信息接收方共享您的个人信息。对我们与之共享个人信息的信息接收方，我们会与其签署保密条款、要求他们按照我们的说明、本政策及其他任何相关的保密和安全措施来处理您的个人信息。信息接收方对您个人信息仅有有限的访问权，他们还承担契约义务，包括(但不限于)使用符合相关法律规定的方式使用通过我们 获得的个人信息或以其他方式代表我们获取的任何个人信息，采取合理的安全 措施(包括适用法律所要求的措施)，并根据适用法律的规定和契约的约定承 担相关的法律责任。<br/>四、我们如何保护您的个人信息<br/>云课网校非常重视信息安全，努力使用各种安全技术和程序以防信息的丢失、 不当使用、未经授权阅览或披露。例如，在某些服务中，将利用加密技术来保 护用户向云课网校提供的个人信息。请您理解，由于技术的限制及风险防范的局限，即使我们尽力加强安全措施，也无法始终保证信息百分之百的安全。您需要了解，用户接入互联网服务所使用的系统和通讯网络，有可能因我们可控范围外的情况而发生问题。<br/>1、用户的账户均有安全保护功能，用户应采取积极措施保护个人信息的安全， 包括但不限于使用复杂密码、定期修改密码、不将自己的账号密码等个人信息 透漏给他人等。<br/>2、除非经过用户同意，我们不允许任何用户、第三方通过我们收集、出售或者传播用户信息。<br/>3、云课网校网站含有到其他网站的链接，我们不对那些网站的隐私保护措施负责。当用户登陆那些网站时，请提高警惕，保护个人隐私。<br/>4、在不幸发生个人信息安全事件后，我们将按照法律法规的要求，及时向您告知:安全事件的基本情况和可能的影响、我们已采取或将要采取的处置措施、 您可自主防范和降低风险的建议、对您的补救措施等。我们同时将及时将事件相关情况以邮件、信函、短信、电话、推送通知等方式告知您，难以逐一告知个人信息主体时，我们会采取合理、有效的方式发布公告。同时，我们还将按 照监管部门要求，主动上报个人信息安全事件的处置情况。<br/>5、当因第三方SDK收集或发生收购等情况，而导致途途课堂与第三方成为共同信息控制者时，云课网校会通过合同等形式与第三方共同确定个人信息安全要求，并且在个人信息安全方面承担相应的责任和义务，并向您明确告知。开源的SDK出现个人信息泄露等侵害用户权益的情况，途途课堂承担相应责任。<br/>五、如何管理您的个人信息<br/>我们鼓励您更新和修改您的个人信息以使其更准确有效，也请您理解，您更 正、删除、撤回授权或停止使用云课网校服务的决定，并不影响我们此前基于 您的授权而开展的个人信息处理。除法律法规另有规定，当您更正、删除您的 个人信息或申请注销账号时，我们可能不会<br/>立即从备份系统中更正或删除相应 的个人信息，但会在备份更新时更正或删除这些个人信息。您可以通过以下方 式来管理您的个人信息:<br/>访问、查询、更正、删除您的个人信息:如果您希望查询访问或更改您的账户中的个人信息 (包括昵称、头像、性别、年级、地区、电话)，您可以登录“云课网校App-我的”进行修改。<br/>在以下情形中，您可以向我们提出删除个人信息的请求:<br/>(1)如果我们处理个人信息的行为违反法律法规;<br/>(2)如果我们收集、使用您的个人信息，却未征得您的授权同意;<br/>(3)如果我们处理个人信息的行为严重违反了与您的约定;<br/>(4)如果您不再使用我们的产品或服务，或您注销了账号;<br/>(5)如果我们不再为您提供删除的产品或服务。<br/>若我们决定响应您的删除请求，我们还将同时尽可能通知从我们处获得您的个 人信息的实体，要求其及时删除，除非法律法规另有规定，或这些实体获得您的独立授权。<br/>3、撤回授权您的个人信息:部分个人信息是使用我们服务所必需的，但大多数 其他个人信息的提供是由您决定的。您可以通过解除绑定、修改个人设置、删除相关信息等方式撤回部分授权，也可以通过关闭功能的方式撤销授权。当您撤回同意或授权后，我们无法继续为您提供撤回同意或授权所对应的服务，也将不再处理您相应的个人信息。但您撤回同意或授权的决定，不会影响此前基于您的同意或授权而开展的个人信息处理，我们会在30天内响应、做出答复或合理解释。<br/>4、注销您的账号：您可以进入“删除App-设置-账户安全“，自行发起账号注销。成功发起自主注销后，您可以实时完成注销。<br/>六、我们使用的第三方 SDK 服务<br/>为实现本隐私政策中声明的功能，我们可能会接入第三方服务商提供的 SDK 或其他类似的应用程序，并将我们依照本隐私政策收集的个人信息共享给第三方服务商，以完善课程服务和用户体验。目前，我们接入的第三方服务商主要包括:<br/><br/><br/>如上所述服务由相关的第三方负责运营。您使用这些第三方服务(包括您向这些第三方提供的任何个人信息)，须受第三方自己的服务条款及个人信息保护声明(而非本隐私政策)的约束，您需要仔细阅读其条款。我们仅会出于正当、必要、特定的目的共享您的信息。我们会要求他们履行相关保密义务并采 取相应的安全措施。<br/>七、我们如何处理未成年人的个人信息<br/>我们非常重视未成年人的信息保护，如您为未成年人，建议您请您的父母或监 护人仔细阅读本隐私权政策，并在征得您的父母或监护人同意的前提下使用我 们的服务或向我们提供信息。对于经父母或监护人同意使用我们的产品或服务 而收集未成年人个人信息的情况，我们只<br/>会在法律法规允许，父母或监护人明 确同意或者保护未成年人所必要的情况下收集、使用、共享、转让或公开披露此信息。我 们将遵循正当必要、知情同意、目的明确、安全保障、依法利用的原则，根据 国家相关法律法规及本政策的规定保护未成年人的个人信息。如您的监<br/>护人不同意本隐私政策，您应立即停止使用途途课堂的服务并拒绝提供个人信息，需要进行修改或删除处理的，请随时与我们联系。特别地，若你是14周岁以下的儿童，我们还专为你制定了《儿童隐私保护声明》，儿童及其监护人在为14周岁以下的儿童完成账号注册前，还应仔细阅读我们专门制定的《儿童隐私保护声明》。《儿童隐私保护声明》是适用于14周岁以下的儿童用户的信息保护规则，该政策是在《隐私政策》基础上制定的特别规则，如其与本政策有不一致之处，以《儿童隐私保护声明》为准。<br/>八、您的个人信息的跨境转移<br/>我们在中华人民共和国境内收集和产生的个人信息将存储在中华人民共和国境内，这意味着，原则上您的个人信息不会被转移到您使用产品或服务所在国家/地区的境外管辖区。<br/>九、为保障用户进行实名认证的自由权利<br/>根据《教育部等六部门关于规范校外线上培训的实施意见》，为保障您进行实名认证的自由权利，您可以在本平台输入您的身份证号进行实名认证。您的身份证号将仅用于实名认证。但是，请您知悉并充分理解，在删除APP中您可能无法查询到您提供的身份证号信息和真<br/>实姓名，您也可能无法修改您提供的身份证号信息和真实姓名，如需修改您提供的身份证号信息或真实姓名，您可以联系客服进行处理。<br/>十、隐私政策的修订<br/>我们可能适时修订本隐私政策的条款，本隐私政策为《途途课堂用户协议》的 重要组成部分。对于重大变更，我们会提供更显著的通知，您如果不同意该等 变更，可以选择停止使用删除的产品或服务;如您仍然继续使用删除的产品或服务，即表示同意受经修订的本政策的约束。<br/>十一、法律适用与管辖<br/>本协议之效力、解释、变更、执行与争议解决均适用中华人民共和国法律。因本协议产生的争议，均应按照中华人民共和国法律予以处理，并由被告住所地人民法院管辖。<br/>十二、联系我们<br/>如您对本隐私政策有任何问题、投诉、建议，通过以下方式与我们联系，我们将在 15 个工作日内回复:<br/>邮箱: yunknet@126.com<br/></p>', '2022-04-23 02:34:34');

DROP TABLE IF EXISTS `edu_appadmin`;
CREATE TABLE IF NOT EXISTS `edu_appadmin` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `downtime` int(5) NOT NULL COMMENT '下载次数',
  `version` varchar(30) NOT NULL COMMENT '版本',
  `notes` text NOT NULL COMMENT '版本',
  `platform` varchar(20) NOT NULL COMMENT '平台',
  `downurl` varchar(200) NOT NULL COMMENT '更新内容',
  `addtime` datetime NOT NULL COMMENT '添加时间',
  `type` varchar(10) NOT NULL DEFAULT 'all' COMMENT '升级包类型',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '是否发布',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='APP管理';

DROP TABLE IF EXISTS `edu_appcomment`;
CREATE TABLE IF NOT EXISTS `edu_appcomment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `comment` varchar(150) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='APP评论';

INSERT INTO `edu_appcomment` (`id`, `username`, `comment`, `addtime`) VALUES
(4, '深渊的那支花', '利用碎片化时间阅读，能掌握一点是一点', '2022-07-25 10:17:22'),
(5, '窗边的小豆豆', '这是一个可以学习和放松的平台，感谢为我提供记录学习的机会', '2022-07-25 10:17:36'),
(6, '偷得浮生', '天天打卡，自信心满满，未来还要和蝶变一起学习，一起进步哦', '2022-07-25 10:17:49'),
(7, '大师兄', '更新后的版本，增加了很多功能，很心水，我要一点点探索喽', '2022-07-25 10:18:07'),
(8, '晨光', '每天刷一刷，掌握最新的高考实时热点', '2022-07-25 10:18:21'),
(9, 'Don be cry', '见证了我学习上的进步，现在高三，未来要和蝶变一起努力，做更好的自己', '2022-07-25 10:18:35'),
(10, '咚咚', '每一天都有收获啊', '2022-07-25 10:18:47'),
(12, '不想取名字了', '感谢陪伴，为我提供动力，支撑我整个高三学习生活', '2022-07-25 10:19:15'),
(13, '名字真好', '每天刷一刷，掌握最新的高考实时热点', '2022-12-11 07:54:03');

DROP TABLE IF EXISTS `edu_appshare`;
CREATE TABLE IF NOT EXISTS `edu_appshare` (
  `id` int(11) NOT NULL,
  `detailes` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='APP分享';

INSERT INTO `edu_appshare` (`id`, `detailes`) VALUES
(1, 'a:8:{s:13:\"comment_times\";s:5:\"12342\";s:13:\"install_times\";s:1:\"5\";s:7:\"app_age\";s:1:\"5\";s:11:\"share_img_1\";s:59:\"/upload/image/20220726/b147e38495908aa99e15901fbb7697b9.jpg\";s:11:\"share_img_2\";s:59:\"/upload/image/20220726/4f16a607fd945a6fcbe0176abee9c73c.jpg\";s:11:\"share_img_3\";s:59:\"/upload/image/20220726/272a3a355d196b33ddd3735b6c4327e1.jpg\";s:11:\"share_img_4\";s:59:\"/upload/image/20220726/f46d23aabb17a22bb5927b0e2250c35e.jpg\";s:11:\"share_img_5\";s:59:\"/upload/image/20220726/ddc8206e3fcee68f058ee1724ad70a34.jpg\";}');

DROP TABLE IF EXISTS `edu_article`;
CREATE TABLE IF NOT EXISTS `edu_article` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) UNSIGNED DEFAULT '0' COMMENT '分类ID',
  `uid` int(5) DEFAULT NULL,
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `image` varchar(255) DEFAULT NULL COMMENT '图片',
  `author` varchar(255) DEFAULT NULL COMMENT '作者',
  `summary` text COMMENT '简介',
  `photo` text COMMENT '相册',
  `content` longtext COMMENT '内容',
  `view` int(11) UNSIGNED DEFAULT '0' COMMENT '点击量',
  `is_top` tinyint(1) DEFAULT '0' COMMENT '是否置顶',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否推荐',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `sort_order` int(11) DEFAULT '100' COMMENT '排序',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章' ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `edu_auth_group`;
CREATE TABLE IF NOT EXISTS `edu_auth_group` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT '1',
  `rules` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='权限组';

INSERT INTO `edu_auth_group` (`id`, `name`, `description`, `status`, `rules`) VALUES
(1, '超级管理员', '超级管理员拥有超级权限', 1, '4,12,130,178,179,180,164,14,19,20,21,205,13,45,6,127,128,139,44,56,59,66,67,68,69,103,104,105,106,107,108,109,110,111,112,113,114,115,153,154,224,225,226,60,74,75,76,77,155,156,157,158,159,160,161,162,62,63,64,65,136,137,138,151,152,163,176,177,181,182,185,183,184,211,212,213,214,215,82,83,142,143,144,86,87,88,216,217,218,85,89,148,149,84,91,93,145,146,147,210,219,243,2,9,28,29,30,202,239,240,241,242,10,54,101,123,150,230,231,232,233,235,236,237,238,124,78,94,96,1,8,34,35,36,7,31,32,33,58,79,81,116,119,120,121,122,131,133,134,250,256,257,258,255,80,70,72,165,166,167,172,173,186,187,188,189,190,174,191,192,193,194,195,175,196,197,199,198,200,201,5,16,17,15,22,23,24,18,53,3,220,221,222,223,11,25,26,27,46,47,48,49,50,51,52'),
(2, '教师组', '教师组', 1, '6,127,128,129,139,44,43,56,59,66,67,68,69,103,104,105,106,107,108,109,110,111,112,113,114,115,153,154,224,225,60,74,75,76,77,155,156,157,158,159,160,161,162,249,62,259,63,64,65,247,248,136,137,151,152,163,176,177,181,182,185,183,184,211,212,213,214,215,82,83,260,142,143,144,86,265,87,88,216,217,218,85,89,148,149,84,266,91,93,145,146,147,210,219,243,253,254,267,268,1,8,34,35,36,7,31,32,33,58,79,81,116,117,118,119,120,121,122,131,132,133,134,135,250,256,257,258,255,3,220,221');

DROP TABLE IF EXISTS `edu_auth_group_access`;
CREATE TABLE IF NOT EXISTS `edu_auth_group_access` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `uid` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='权限授权';

INSERT INTO `edu_auth_group_access` (`id`, `uid`, `group_id`) VALUES
(1, 1, 1);

DROP TABLE IF EXISTS `edu_auth_rule`;
CREATE TABLE IF NOT EXISTS `edu_auth_rule` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `icon` varchar(64) DEFAULT '',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `type` char(4) DEFAULT '' COMMENT 'nav,auth',
  `index` tinyint(1) DEFAULT '0' COMMENT '快捷导航',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8 COMMENT='权限规则';

INSERT INTO `edu_auth_rule` (`id`, `pid`, `name`, `url`, `icon`, `sort_order`, `type`, `index`, `status`) VALUES
(1, 0, '文章', '', 'fa fa-book', 5, 'nav', 0, 1),
(2, 0, '师生', '', 'fa fa-users', 3, 'nav', 0, 1),
(3, 0, '扩展', '', 'fa fa-puzzle-piece', 9, 'nav', 0, 1),
(4, 0, '设置', '', 'fa fa-gear', 0, 'nav', 0, 1),
(5, 0, '权限', '', 'fa fa-lock', 8, 'nav', 0, 1),
(6, 0, '控制台', 'admin/index/index', '', 1, 'auth', 0, 1),
(7, 1, '分类管理', 'admin/category/index', 'fa fa-navicon', 2, 'nav', 1, 1),
(8, 1, '文章管理', 'admin/article/index', 'fa fa-book', 1, 'nav', 1, 1),
(9, 2, '学员管理', 'admin/user/index', 'fa fa-users', 0, 'nav', 1, 1),
(10, 2, '学员日志', 'admin/user/log', 'fa fa-clock-o', 0, 'nav', 0, 1),
(11, 3, '广告管理', 'admin/ad/index', 'fa fa-image', 1, 'nav', 1, 1),
(12, 4, '基本设置', 'admin/config/setting', 'fa fa-cog', 1, 'nav', 1, 1),
(13, 4, '系统设置', 'admin/config/system', 'fa fa-wrench', 3, 'nav', 0, 1),
(14, 4, '设置管理', 'admin/config/index', 'fa fa-bars', 2, 'nav', 0, 1),
(15, 5, '权限规则', 'admin/auth/rule', 'fa fa-th-list', 3, 'nav', 0, 1),
(16, 5, '管理员', 'admin/admin/index', 'fa fa-user', 0, 'nav', 0, 1),
(17, 5, '权限组', 'admin/auth/group', 'fa fa-users', 1, 'nav', 0, 1),
(18, 5, '管理员日志', 'admin/admin/log', 'fa fa-clock-o', 5, 'nav', 0, 1),
(19, 14, '添加', 'admin/config/add', '', 0, 'auth', 0, 1),
(20, 14, '编辑', 'admin/config/edit', '', 0, 'auth', 0, 1),
(21, 14, '删除', 'admin/config/del', '', 0, 'auth', 0, 1),
(22, 15, '添加', 'admin/auth/addRule', '', 0, 'auth', 0, 1),
(23, 15, '编辑', 'admin/auth/editRule', '', 0, 'auth', 0, 1),
(24, 15, '删除', 'admin/auth/delRule', '', 0, 'auth', 0, 1),
(25, 11, '添加', 'admin/ad/add', '', 0, 'auth', 0, 1),
(26, 11, '编辑', 'admin/ad/edit', '', 0, 'auth', 0, 1),
(27, 11, '删除', 'admin/ad/del', '', 0, 'auth', 0, 1),
(28, 9, '添加', 'admin/user/add', '', 0, 'auth', 0, 1),
(29, 9, '编辑', 'admin/user/edit', '', 0, 'auth', 0, 1),
(30, 9, '删除', 'admin/user/del', '', 0, 'auth', 0, 1),
(31, 7, '添加', 'admin/category/add', '', 0, 'auth', 0, 1),
(32, 7, '编辑', 'admin/category/edit', '', 0, 'auth', 0, 1),
(33, 7, '删除', 'admin/category/del', '', 0, 'auth', 0, 1),
(34, 8, '添加', 'admin/article/add', '', 0, 'auth', 0, 1),
(35, 8, '编辑', 'admin/article/edit', '', 0, 'auth', 0, 1),
(36, 8, '删除', 'admin/article/del', '', 0, 'auth', 0, 1),
(37, 16, '添加', 'admin/admin/add', '', 0, 'auth', 0, 1),
(38, 16, '编辑', 'admin/admin/edit', '', 0, 'auth', 0, 1),
(39, 16, '删除', 'admin/admin/del', '', 0, 'auth', 0, 1),
(40, 17, '添加', 'admin/auth/addGroup', '', 0, 'auth', 0, 1),
(41, 17, '编辑', 'admin/auth/editGroup', '', 0, 'auth', 0, 1),
(42, 17, '删除', 'admin/auth/delGroup', '', 0, 'auth', 0, 1),
(43, 6, '修改密码', 'admin/index/editPassword', '', 2, 'auth', 0, 1),
(44, 6, '清除缓存', 'admin/index/clear', '', 1, 'auth', 0, 1),
(45, 4, '上传设置', 'admin/config/upload', 'fa fa-upload', 4, 'nav', 0, 1),
(46, 3, '数据管理', 'admin/database/index', 'fa fa-database', 4, 'nav', 1, 1),
(47, 46, '还原', 'admin/database/import', '', 0, 'auth', 0, 1),
(48, 46, '备份', 'admin/database/backup', '', 0, 'auth', 0, 1),
(49, 46, '优化', 'admin/database/optimize', '', 0, 'auth', 0, 1),
(50, 46, '修复', 'admin/database/repair', '', 0, 'auth', 0, 1),
(51, 46, '下载', 'admin/database/download', '', 0, 'auth', 0, 1),
(52, 46, '删除', 'admin/database/del', '', 0, 'auth', 0, 1),
(53, 18, '一键清空', 'admin/admin/truncate', '', 0, 'auth', 0, 1),
(54, 10, '一键清空', 'admin/user/truncate', '', 0, 'auth', 0, 1),
(56, 0, '课程', 'admin/course/videoindex', 'fa fa-video-camera', 1, 'nav', 0, 1),
(58, 0, '教育云', 'admin/educloud/videoList', 'fa fa-cloud', 6, 'nav', 1, 1),
(59, 56, '点播课程', 'admin/course/videoindex', 'fa fa-film', 1, 'nav', 1, 1),
(60, 56, '直播课程', 'admin/course/liveindex', 'fa fa-video-camera', 2, 'nav', 1, 1),
(62, 56, '课程分类', 'admin/course/coursecategory', 'fa fa-th-list', 4, 'nav', 0, 1),
(63, 62, '添加', 'admin/course/categoryadd', '', 0, 'auth', 0, 1),
(64, 62, '编辑', 'admin/course/categoryedit', '', 0, 'auth', 0, 1),
(65, 62, '删除', 'admin/course/categorydel', '', 0, 'auth', 0, 1),
(66, 59, '添加', 'admin/course/videoadd', '', 0, 'auth', 0, 1),
(67, 59, '编辑', 'admin/course/videoedit', '', 0, 'auth', 0, 1),
(68, 59, '删除', 'admin/course/videodel', '', 0, 'auth', 0, 1),
(69, 59, '管理', 'admin/course/videoadmin', '', 0, 'auth', 0, 1),
(70, 58, '云直播', 'admin/educloud/index', 'fa fa-address-card', 1, 'nav', 0, 1),
(72, 70, '账号绑定', 'admin/educloud/liveBind', 'fa fa-plus-square', 3, 'nav', 0, 1),
(74, 60, '添加', 'admin/course/liveAdd', '', 0, 'auth', 0, 1),
(75, 60, '编辑', 'admin/course/liveEdit', '', 0, 'auth', 0, 1),
(76, 60, '删除', 'admin/course/liveDel', '', 0, 'auth', 0, 1),
(77, 60, '管理', 'admin/course/liveAdmin', '', 0, 'auth', 0, 1),
(78, 0, '问答', 'admin/forum/plate', 'fa fa-coffee', 4, 'nav', 0, 1),
(79, 58, '云点播', '', 'fa fa-video-camera', 0, 'nav', 0, 1),
(80, 79, '账号绑定', 'admin/educloud/videoBind', 'fa fa-plus-circle', 1, 'nav', 0, 1),
(81, 79, '云视频', 'admin/educloud/videoList', 'fa fa-bars', 0, 'nav', 1, 1),
(82, 0, '题库', 'admin/exam/questionsList', 'fa fa-sticky-note-o', 2, 'nav', 0, 1),
(83, 82, '试题管理', 'admin/exam/index', 'fa fa-bars', 0, 'nav', 0, 1),
(84, 82, '试卷管理', 'admin/exam/typelist', 'fa fa-sitemap', 0, 'nav', 0, 1),
(85, 83, '题型管理', 'admin/exam/typeList', 'fa fa-pencil-square-o', 2, 'nav', 0, 1),
(86, 83, '试题列表', 'admin/exam/questionsList', 'fa fa-navicon', 1, 'nav', 1, 1),
(87, 86, '单题添加', 'admin/exam/questionsSingleAdd', '', 0, 'auth', 0, 1),
(88, 86, '批量导入', 'admin/exam/questionsCSVleAdd', '', 0, 'nav', 0, 1),
(89, 85, '添加类型', 'admin/exam/typeAdd', '', 0, 'nav', 0, 1),
(91, 84, '试卷列表', 'admin/exam/examList', 'fa fa-navicon', 0, 'nav', 1, 1),
(93, 84, '手动组卷', 'admin/exam/selfpage', '', 0, 'nav', 0, 1),
(94, 78, '板块管理', 'admin/forum/plate', 'fa fa-cube', 0, 'nav', 1, 1),
(96, 94, '添加板块', 'admin/forum/addplate', '', 0, 'auth', 0, 1),
(97, 70, '直播间管理', 'admin/educloud/getliveroomlist', 'fa fa-institution', 2, 'nav', 1, 0),
(98, 97, '删除直播间', 'admin/educloud/delLiveroom', '', 0, 'auth', 0, 1),
(99, 70, '回放管理', 'admin/educloud/getplaybacklist', 'fa fa-window-restore', 2, 'nav', 1, 0),
(100, 99, '删除回放', 'admin/educloud/delPlayback', '', 0, 'auth', 0, 1),
(101, 2, '教师管理', 'admin/user/teacher', 'fa fa-street-view', 0, 'nav', 0, 1),
(103, 69, '添加章', 'admin/course/videoAddZhang', '', 0, 'auth', 0, 1),
(104, 69, '编辑章', 'admin/course/videoEditZhang', '', 0, 'auth', 0, 1),
(105, 69, '添加视频课时', 'admin/course/videoAddSection', '', 0, 'auth', 0, 1),
(106, 69, '编辑视频课时', 'admin/course/videoEditSection', '', 0, 'auth', 0, 1),
(107, 69, '删除视频课时', 'admin/course/videoDelSection', '', 0, 'auth', 0, 1),
(108, 69, '视频列表', 'admin/course/videoList', '', 0, 'auth', 0, 1),
(109, 69, '添加文本课程', 'admin/course/videoAddDoc', '', 0, 'auth', 0, 1),
(110, 69, '编辑文本课程', 'admin/course/videoEditDoc', '', 0, 'auth', 0, 1),
(111, 69, '学员列表', 'admin/course/xueyuanList', '', 0, 'auth', 0, 1),
(112, 69, '资料列表', 'admin/course/materialList', '', 0, 'auth', 0, 1),
(113, 69, '添加资料', 'admin/course/MaterialAdd', '', 0, 'auth', 0, 1),
(114, 69, '删除资料关联', 'admin/course/videoMaterialDel', '', 0, 'auth', 0, 1),
(115, 69, '向课程中添加资料', 'admin/course/MaterialInsert', '', 0, 'auth', 0, 1),
(116, 81, '视频列表', 'admin/educloud/videoList', '', 0, 'auth', 0, 1),
(117, 81, '上传视频', 'admin/educloud/videoup', '', 0, 'auth', 0, 1),
(118, 81, '删除云视频', 'admin/educloud/videodel', '', 0, 'auth', 0, 1),
(119, 81, 'Oss上传类', 'admin/educloud/new_oss', '', 0, 'auth', 0, 1),
(120, 81, 'Oss上传实例', 'admin/educloud/ossupload', '', 0, 'auth', 0, 1),
(121, 81, '删除OSS文件', 'admin/educloud/ossdel', '', 0, 'auth', 0, 1),
(122, 81, '获取上传凭证', 'admin/educloud/getaliuptoken', '', 0, 'auth', 0, 1),
(123, 101, '教师列表', 'admin/user/teacherList', '', 0, 'nav', 0, 1),
(124, 101, '申请条例', 'admin/user/ordinance', '', 1, 'auth', 0, 1),
(127, 6, '修改教师信息', 'admin/index/teacherInfoSave', '', 0, 'auth', 0, 1),
(128, 6, '上传图片', 'admin/index/uploadImage', '', 0, 'auth', 0, 1),
(129, 6, '上传文件', 'admin/index/uploadFile', '', 0, 'auth', 0, 1),
(130, 4, '支付设置', 'admin/config/pay', 'fa fa-shopping-cart', 1, 'nav', 0, 1),
(131, 81, '视频分类列表', 'admin/educloud/videocategory', '', 0, 'auth', 0, 1),
(132, 81, '添加视频分类', 'admin/educloud/addvideocategory', '', 0, 'auth', 0, 1),
(133, 81, '获取视频分类列表', 'admin/educloud/videocategory', '', 0, 'auth', 0, 1),
(134, 81, '添加提交', 'admin/educloud/addCategoryPhpSDK', '', 0, 'auth', 0, 1),
(135, 81, '删除视频分类', 'admin/educloud/delvideocategory', '', 0, 'nav', 0, 1),
(136, 56, '课程订单', 'admin/course/courseOrder', 'fa fa-building-o', 5, 'nav', 1, 1),
(137, 136, '订单列表', 'admin/course/courseOrder', '', 0, 'auth', 0, 1),
(138, 136, '删除订单', 'admin/course/delCourseOrder', '', 0, 'auth', 0, 1),
(139, 6, '提现', 'admin/user/tixian', '', 0, 'nav', 0, 1),
(142, 83, '删除试题', 'admin/exam/questionsDel', '', 0, 'auth', 0, 1),
(143, 83, '编辑试题', 'admin/exam/questionsEdit', '', 0, 'auth', 0, 1),
(144, 83, '试题预览', 'admin/exam/questionsPreview', '', 0, 'auth', 0, 1),
(145, 84, '试卷预览', 'admin/exam/examPreview', '', 0, 'auth', 0, 1),
(146, 84, '删除试卷', 'admin/exam/examDel', '', 0, 'auth', 0, 1),
(147, 84, '选择试题', 'admin/exam/questionsSelect', '', 0, 'auth', 0, 1),
(148, 85, '编辑题型', 'admin/exam/typeEdit', '', 0, 'auth', 0, 1),
(149, 85, '删除题型', 'admin/exam/typeDel', '', 0, 'auth', 0, 1),
(150, 101, '提现管理', 'admin/user/tixianAdmin', 'fa fa-diamond', 0, 'nav', 0, 1),
(151, 56, '批改作业', 'admin/exam/paperList', 'fa fa-window-restore', 6, 'nav', 0, 1),
(152, 151, '作业列表', 'admin/exam/paperlist', '', 0, 'auth', 0, 1),
(153, 69, '添加试卷', 'admin/course/videoaddExam', '', 0, 'auth', 0, 1),
(154, 69, '试卷列表', 'admin/course/paperlist', '', 0, 'nav', 0, 1),
(155, 77, '添加章', 'admin/course/liveAddZhang', '', 0, 'auth', 0, 1),
(156, 77, '编辑章', 'admin/course/liveEditZhang', '', 0, 'auth', 0, 1),
(157, 77, '创建直播间', 'admin/course/liveAddSection', '', 0, 'auth', 0, 1),
(158, 77, '编辑直播间', 'admin/course/liveEditSection', '', 0, 'auth', 0, 1),
(159, 77, '删除直播间课时', 'admin/course/liveDelSection', '', 0, 'auth', 0, 1),
(160, 77, '添加文本课时', 'admin/course/liveAddDoc', '', 0, 'auth', 0, 1),
(161, 77, '编辑文本课时', 'admin/course/liveEditDoc', '', 0, 'auth', 0, 1),
(162, 77, '添加考试', 'admin/course/liveaddExam', '', 0, 'auth', 0, 1),
(163, 151, '批改作业', 'admin/exam/mark', '', 0, 'auth', 0, 1),
(164, 4, '登录设置', 'admin/config/thirdLogin', 'fa fa-sign-in', 1, 'nav', 0, 0),
(165, 58, '云短信', 'admin/educloud/cloudSMS', 'fa fa-recycle', 2, 'nav', 0, 1),
(166, 165, '签名配置', 'admin/educloud/signName', '', 0, 'nav', 0, 1),
(167, 165, '模板配置', 'admin/educloud/templates', '', 0, 'nav', 0, 1),
(172, 0, '营销', '', 'fa fa-line-chart', 7, 'nav', 0, 1),
(173, 172, '充值卡', 'admin/card/index', 'fa fa-map-o', 0, 'nav', 0, 1),
(174, 172, '优惠券', 'admin/coupon/index', 'fa fa-square-o', 0, 'nav', 0, 1),
(175, 172, '限时抢购', 'admin/flashsale/index', 'fa fa-bolt', 0, 'nav', 0, 1),
(176, 56, '班级管理', 'admin/classroom/index', 'fa fa-database', 3, 'nav', 0, 1),
(177, 176, '添加班级', 'admin/classroom/add', '', 0, 'auth', 0, 1),
(178, 130, '微信支付', 'admin/config/pay/type/1', '', 0, 'nav', 0, 1),
(179, 130, '支付宝支付', 'admin/config/pay/type/2', '', 0, 'nav', 0, 1),
(180, 130, '教师分成比例', 'admin/config/pay/type/3', '', 0, 'nav', 0, 1),
(181, 176, '班级列表', 'admin/classroom/index', '', 0, 'auth', 0, 1),
(182, 176, '编辑班级', 'admin/classroom/edit', '', 0, 'auth', 0, 1),
(183, 176, '删除班级', 'admin/classroom/del', '', 0, 'auth', 0, 1),
(184, 176, '班级课程', 'admin/classroom/courseList', '', 0, 'auth', 0, 1),
(185, 182, '提交编辑', 'admin/classroom/editPost', '', 0, 'auth', 0, 1),
(186, 173, '列表', 'admin/card/index', '', 0, 'auth', 0, 1),
(187, 173, '添加', 'admin/card/add', '', 0, 'auth', 0, 1),
(188, 173, '编辑', 'admin/card/edit', '', 0, 'auth', 0, 1),
(189, 173, '删除', 'admin/card/del', '', 0, 'auth', 0, 1),
(190, 173, '导出', 'admin/card/export', '', 0, 'auth', 0, 1),
(191, 174, '列表', 'admin/coupon/index', '', 0, 'auth', 0, 1),
(192, 174, '添加', 'admin/coupon/add', '', 0, 'auth', 0, 1),
(193, 174, '编辑', 'admin/coupon/edit', '', 0, 'auth', 0, 1),
(194, 174, '删除', 'admin/coupon/del', '', 0, 'auth', 0, 1),
(195, 174, '发放', 'admin/coupon/fafang', '', 0, 'auth', 0, 1),
(196, 175, '列表', 'admin/flashsale/index', '', 0, 'auth', 0, 1),
(197, 175, '添加', 'admin/flashsale/create', '', 0, 'auth', 0, 1),
(198, 175, '编辑', 'admin/flashsale/edit', '', 0, 'auth', 0, 1),
(199, 197, '提交', 'admin/flashsale/createPost', '', 0, 'auth', 0, 1),
(200, 198, '提交', 'admin/flashsale/editPost', '', 0, 'auth', 0, 1),
(201, 175, '删除', 'admin/flashsale/del', '', 0, 'auth', 0, 1),
(202, 9, '给钱', 'admin/user/addmoney', '', 0, 'auth', 0, 1),
(205, 4, '前台菜单', 'admin/config/nav', 'fa fa-sitemap', 2, 'nav', 0, 1),
(206, 205, '添加菜单', 'admin/config/navadd', '', 0, 'auth', 0, 1),
(207, 205, '编辑菜单', 'admin/config/navedit', '', 0, 'auth', 0, 1),
(208, 205, '添加菜单', 'admin/config/navadd', '', 0, 'auth', 0, 1),
(209, 205, '删除菜单', 'admin/config/navdel', '', 0, 'auth', 0, 1),
(210, 84, '导出成绩', 'admin/exam/export', '', 0, 'auth', 0, 1),
(211, 176, '删除学员', 'admin/classroom/detach', '', 0, 'auth', 0, 1),
(212, 176, '导出学员', 'admin/classroom/export', '', 0, 'auth', 0, 1),
(213, 176, '学员列表', 'admin/classroom/xueyuanList', '', 0, 'auth', 0, 1),
(214, 176, '导入学员提交', 'admin/classroom/importExcel', '', 0, 'auth', 0, 1),
(215, 176, '导入学员', 'admin/classroom/import', '', 0, 'auth', 0, 1),
(216, 86, '题帽子题列表', 'admin/exam/listSubQuestions', '', 0, 'auth', 0, 1),
(217, 86, '添加题帽子题', 'admin/exam/addSubQuestions', '', 0, 'auth', 0, 1),
(218, 86, '批量导入提交', 'admin/exam/importExcel', '', 0, 'auth', 0, 1),
(219, 84, '智能组卷', 'admin/exam/intellectpage', '', 0, 'nav', 0, 1),
(220, 3, '合作伙伴', 'admin/link/index', 'fa fa-retweet', 0, 'nav', 0, 1),
(221, 220, '添加', 'admin/link/add', '', 0, 'auth', 0, 1),
(222, 220, '编辑', 'admin/link/edit', '', 0, 'auth', 0, 1),
(223, 220, '删除', 'admin/link/del', '', 0, 'auth', 0, 1),
(224, 69, '评论列表', 'admin/course/commentList', '', 0, 'auth', 0, 1),
(225, 69, '评论回复', 'admin/course/commentsRe', '', 0, 'auth', 0, 1),
(226, 69, '评论删除', 'admin/course/commentsDel', '', 0, 'auth', 0, 1),
(229, 70, '账号详情', 'admin/educloud/liveset', '', 6, 'nav', 0, 0),
(230, 101, '添加教师', 'admin/user/addteacher', '', 0, 'auth', 0, 1),
(231, 101, '导入教师', 'admin/user/importteacher', '', 0, 'auth', 0, 1),
(232, 101, '导出教师', 'admin/user/exportteacher', '', 0, 'auth', 0, 1),
(233, 101, '删除教师申请', 'admin/user/delverify', '', 0, 'auth', 0, 1),
(235, 101, '教师状态设置', 'admin/user/teacherstatus', '', 0, 'auth', 0, 1),
(236, 101, '提现申请', 'admin/user/tixian', '', 0, 'auth', 0, 1),
(237, 101, '提现操作', 'admin/user/tianxianPost', '', 0, 'auth', 0, 1),
(238, 101, '导出提现数据', 'admin/user/exporttixian', '', 0, 'auth', 0, 1),
(239, 9, '列表', 'admin/user/index', '', 0, 'auth', 0, 1),
(240, 9, '导出', 'admin/user/export', '', 0, 'auth', 0, 1),
(241, 9, '导入', 'admin/user/import', '', 0, 'auth', 0, 1),
(242, 9, '导入提交', 'admin/user/importExcel', '', 0, 'auth', 0, 1),
(243, 84, '试卷分析', 'admin/exam/analysis', '', 0, 'auth', 0, 1),
(244, 2, '注册设置', 'admin/user/regfield', 'fa fa-id-badge', 0, 'nav', 0, 1),
(245, 172, 'VIP会员设置', 'admin/vip/setup', 'fa fa-user-secret', 0, 'nav', 0, 1),
(246, 172, '三级分销', 'admin/distribution/setup', 'fa fa-sitemap', 0, 'nav', 0, 1),
(247, 62, '添加知识点', 'admin/course/knowledgeadd', '', 0, 'auth', 0, 1),
(248, 62, '删除知识点', 'admin/course/knowledgedel', '', 0, 'auth', 0, 1),
(249, 77, '直播数据分析', 'admin/course/liveanalyse', '', 0, 'auth', 0, 1),
(253, 84, '考试监控', 'admin/exam/examMonitor', '', 0, 'auth', 0, 1),
(254, 84, '编辑试卷', 'admin/exam/examEdit', '', 0, 'auth', 0, 1),
(259, 62, '知识点列表', 'admin/course/knowledgeList', '', 0, 'auth', 0, 1),
(260, 83, '知识点列表', 'admin/course/ajaxGetKnowledge', '', 0, 'auth', 0, 1),
(262, 3, 'APP管理', 'admin/appadmin/index', 'fa fa-fax', 5, 'nav', 0, 1),
(263, 262, '源码下载', 'admin/appadmin/index', '', 0, 'nav', 0, 1),
(264, 262, '安装包上传', 'admin/appadmin/upload', '', 0, 'nav', 0, 1),
(265, 86, '批量添加', 'admin/exam/questionsMoreAdd', '', 0, 'auth', 0, 1),
(266, 84, '设置试卷是否开放', 'admin/exam/examIsOpen', '', 0, 'auth', 0, 1),
(267, 82, '批阅试卷', 'admin/exam/examsList', 'fa fa-map-o', 1, 'nav', 0, 1),
(268, 267, '开始批阅', 'admin/exam/examsList', '', 0, 'nav', 0, 1),
(269, 262, '用户协议', 'admin/appadmin/agreement', '', 0, 'nav', 0, 1),
(270, 1, '单页管理', 'admin/article/page', 'fa fa-clone', 0, 'nav', 0, 1),
(271, 270, '添加', 'admin/article/pageadd', '', 0, 'auth', 0, 1),
(272, 270, '编辑', 'admin/article/pageadd', '', 0, 'auth', 0, 1),
(273, 270, '删除', 'admin/article/pagedel', '', 0, 'auth', 0, 1),
(274, 4, '积分设置', 'admin/config/jifen', 'fa fa-diamond', 10, 'nav', 0, 1),
(275, 262, 'APP分享设置', 'admin/appadmin/appshare', '', 0, 'nav', 0, 1),
(276, 172, '评论马甲', 'admin/flashsale/comments', 'fa fa-edit', 1, 'nav', 0, 1),
(277, 276, '添加评论', 'admin/flashsale/addComments', '', 0, 'auth', 0, 1),
(278, 176, '发放证书', 'admin/classroom/certificate', '', 0, 'auth', 0, 1),
(279, 78, '问答管理', 'admin/forum/adminplate', 'fa fa-commenting-o', 1, 'nav', 1, 1),
(280, 279, '删除问答', 'admin/forum/adminplate', '', 0, 'auth', 0, 1),
(281, 94, '编辑板块', 'admin/forum/editplate', '', 0, 'auth', 0, 1),
(282, 94, '删除板块', 'admin/forum/delplate', '', 0, 'auth', 0, 1),
(283, 279, '管理问答', 'admin/forum/adminplate', '', 0, 'auth', 0, 1),
(284, 279, '删除问答', 'admin/forum/deltopic', '', 0, 'auth', 0, 1),
(285, 279, '回复管理', 'admin/forum/reply', '', 0, 'auth', 0, 1),
(286, 279, '删除回复', 'admin/forum/delreply', '', 0, 'auth', 0, 1),
(287, 101, '默认教师', 'admin/user/defaultTeacher', '', 0, 'nav', 0, 1),
(288, 56, '线下课程', 'admin/course/OfflineCourse', 'fa fa-language', 3, 'nav', 0, 1),
(289, 288, '添加', 'admin/course/addOfflineCourse', '', 0, 'auth', 0, 1),
(290, 288, '编辑', 'admin/course/editOfflineCourse', '', 0, 'auth', 0, 1),
(291, 288, '删除', 'admin/course/delOfflineCourse', '', 0, 'auth', 0, 1),
(292, 288, '导出', 'admin/course/exportOfflineStu', '', 0, 'auth', 0, 1);

DROP TABLE IF EXISTS `edu_card`;
CREATE TABLE IF NOT EXISTS `edu_card` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `number` varchar(20) DEFAULT NULL,
  `money` int(8) DEFAULT NULL,
  `uid` int(8) DEFAULT NULL,
  `usestatus` int(2) DEFAULT '0',
  `buystatus` int(2) DEFAULT '0',
  `status` int(2) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='点卡';

DROP TABLE IF EXISTS `edu_category`;
CREATE TABLE IF NOT EXISTS `edu_category` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` smallint(5) UNSIGNED DEFAULT '0' COMMENT '上级分类ID',
  `category_name` varchar(255) DEFAULT '' COMMENT '标题',
  `sort_order` int(11) DEFAULT '100' COMMENT '排序',
  `keywords` varchar(255) DEFAULT '' COMMENT '关键字',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='分类' ROW_FORMAT=COMPACT;

INSERT INTO `edu_category` (`id`, `pid`, `category_name`, `sort_order`, `keywords`, `description`) VALUES
(1, 0, '行业资讯', 0, '', ''),
(2, 0, '本站动态', 100, '', '');

DROP TABLE IF EXISTS `edu_certificate`;
CREATE TABLE IF NOT EXISTS `edu_certificate` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `cid` int(5) NOT NULL,
  `eid` int(5) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '0',
  `certificatetitle` varchar(100) NOT NULL,
  `organ` varchar(100) NOT NULL,
  `imgurl` varchar(80) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_classroom`;
CREATE TABLE IF NOT EXISTS `edu_classroom` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '0',
  `type` int(2) DEFAULT NULL,
  `headteacher` int(5) DEFAULT NULL,
  `cids` varchar(200) DEFAULT NULL,
  `views` int(8) DEFAULT '0',
  `status` int(2) DEFAULT '1',
  `is_top` int(2) DEFAULT '0',
  `price` float(7,2) DEFAULT '0.00',
  `picture` varchar(200) DEFAULT '0',
  `xuni` int(7) DEFAULT '0',
  `brief` text,
  `youxiaoqi` int(5) DEFAULT '0',
  `sort_order` int(4) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `iospproductid` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教室';

DROP TABLE IF EXISTS `edu_comment`;
CREATE TABLE IF NOT EXISTS `edu_comment` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `cid` int(6) DEFAULT NULL,
  `sid` int(6) DEFAULT NULL,
  `uid` int(6) DEFAULT NULL,
  `cstype` int(2) DEFAULT NULL,
  `contents` text,
  `replay` text,
  `ruid` int(8) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程评论';

DROP TABLE IF EXISTS `edu_config`;
CREATE TABLE IF NOT EXISTS `edu_config` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分组',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '配置类型',
  `value` text NOT NULL COMMENT '默认值',
  `options` text COMMENT '选项值',
  `sort_order` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='配置' ROW_FORMAT=COMPACT;

INSERT INTO `edu_config` (`id`, `group`, `title`, `name`, `type`, `value`, `options`, `sort_order`, `status`) VALUES
(1, 'website', '网站logo', 'logo', 'image', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/6292bfbb6b2ea.png', '', 96, 1),
(2, 'website', '网站名称', 'site_name', 'input', '云课网校终身版', '', 100, 1),
(3, 'website', '网站标题', 'site_title', 'input', '云课网校系统-最具性价比的网校系统', '', 100, 1),
(4, 'website', '网站关键字', 'site_keywords', 'input', '云课网校系统是一款基于ThinkPhP框架的网校系统，包含直播，点播，考试，问答，论坛，文章等模块，是一款最具性价比的线上学习系统。', '', 100, 1),
(5, 'website', '网站描述', 'site_description', 'textarea', '云课网校系统是一款基于ThinkPhP框架的网校系统，包含直播，点播，考试，问答，论坛，文章等模块，是一款最具性价比的线上学习系统。', '', 100, 1),
(6, 'website', '版权信息', 'site_copyright', 'input', '云课网络科技有限公司', '', 100, 1),
(7, 'website', 'ICP备案号', 'site_icp', 'input', '', '', 100, 1),
(8, 'website', '统计代码', 'site_code', 'textarea', '', '', 100, 1),
(9, 'contact', '公司名称', 'company', 'input', '知了数学', '', 100, 1),
(10, 'contact', '公司地址', 'address', 'input', '山东省济宁市邹城市', '', 100, 1),
(11, 'contact', '联系电话', 'tel', 'input', '18353738667', '', 100, 1),
(12, 'contact', '联系邮箱', 'email', 'input', 'yunke_tp5@126.net', '', 100, 1),
(15, 'website', '登录页关注的二维码', 'weixin', 'image', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/20220317144007.png', '', 99, 1),
(24, 'website', '关于我们', 'about', 'textarea', '云课网校系统是一款基于ThinkPhP框架的网校系统，包含直播，点播，考试，问答，论坛，文章等模块，是一款最具性价比的线上学习系统。', '', 100, 1),
(25, 'website', '网站icon', 'icon', 'image', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/favicon .ico', '', 98, 1),
(26, 'contact', '客服QQ', 'QQ', 'input', '378146005', '', 100, 1),
(27, 'website', '底部logo', 'big_logo', 'image', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/cc76b045db61afd4085dcba83e0faf0b.png', '', 97, 1),
(29, 'website', '公安备案', 'police_icp', 'input', '', '', 100, 1),
(30, 'website', '底部微信小程序', 'xiaochengxu', 'image', '//yunkcloud.oss-cn-beijing.aliyuncs.com/files1/image/20220317144007.png', '', 99, 1);

DROP TABLE IF EXISTS `edu_cooperate`;
CREATE TABLE IF NOT EXISTS `edu_cooperate` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `admin_id` int(5) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `sex` varchar(6) DEFAULT '0',
  `birth_y` varchar(10) DEFAULT NULL,
  `birth_m` varchar(10) DEFAULT NULL,
  `birth_d` varchar(10) DEFAULT NULL,
  `provid` varchar(10) DEFAULT NULL,
  `cityid` varchar(20) DEFAULT NULL,
  `areaid` varchar(20) DEFAULT NULL,
  `identity` varchar(25) DEFAULT NULL,
  `identity_z` varchar(100) DEFAULT NULL,
  `identity_f` varchar(100) DEFAULT NULL,
  `certificate` varchar(100) DEFAULT NULL,
  `brief` varchar(400) DEFAULT NULL,
  `sign` varchar(150) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师申请';

DROP TABLE IF EXISTS `edu_coupon`;
CREATE TABLE IF NOT EXISTS `edu_coupon` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `youxiaoqi` int(8) NOT NULL,
  `rate` int(11) DEFAULT NULL,
  `status` int(2) DEFAULT '0',
  `buystatus` int(2) DEFAULT '0',
  `usestatus` int(2) DEFAULT '0',
  `userId` int(6) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `useaddtime` datetime DEFAULT NULL,
  `userendtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券';

DROP TABLE IF EXISTS `edu_course_category`;
CREATE TABLE IF NOT EXISTS `edu_course_category` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` smallint(5) UNSIGNED DEFAULT '0' COMMENT '上级分类ID',
  `category_name` varchar(255) DEFAULT NULL COMMENT '标题',
  `appname` varchar(10) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '100' COMMENT '排序',
  `keywords` varchar(255) DEFAULT '' COMMENT '关键字',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类' ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `edu_dianming`;
CREATE TABLE IF NOT EXISTS `edu_dianming` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(8) NOT NULL,
  `sid` varchar(15) NOT NULL,
  `uid` int(10) NOT NULL,
  `times` int(3) NOT NULL,
  `addtimes` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播课程点名答到数据';

DROP TABLE IF EXISTS `edu_distribution`;
CREATE TABLE IF NOT EXISTS `edu_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `fatherid` int(11) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销列表';

DROP TABLE IF EXISTS `edu_distributionprofit`;
CREATE TABLE IF NOT EXISTS `edu_distributionprofit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL,
  `fid` int(7) NOT NULL,
  `cid` int(7) DEFAULT NULL,
  `typeid` int(2) NOT NULL,
  `profit` float(8,2) DEFAULT '0.00',
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销提成';

DROP TABLE IF EXISTS `edu_exams`;
CREATE TABLE IF NOT EXISTS `edu_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(5) DEFAULT NULL,
  `isopen` int(2) DEFAULT NULL,
  `examsubject` tinyint(4) DEFAULT '0',
  `exam` varchar(120) DEFAULT '',
  `examsetting` text,
  `examtype` int(2) DEFAULT NULL,
  `examquestions` text,
  `examscore` text,
  `examstatus` int(1) DEFAULT '0',
  `examauthorid` int(11) DEFAULT '0',
  `examtime` int(11) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `examstatus` (`examstatus`),
  KEY `examtype` (`examauthorid`),
  KEY `examtime` (`examtime`),
  KEY `examsubject` (`examsubject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试卷';

DROP TABLE IF EXISTS `edu_exercise`;
CREATE TABLE IF NOT EXISTS `edu_exercise` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) DEFAULT NULL,
  `examquestions` text,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='章节练习抽题';

DROP TABLE IF EXISTS `edu_favourite`;
CREATE TABLE IF NOT EXISTS `edu_favourite` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `uid` int(5) DEFAULT NULL,
  `cid` int(5) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏';

DROP TABLE IF EXISTS `edu_forum_plate`;
CREATE TABLE IF NOT EXISTS `edu_forum_plate` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `isshow` int(2) DEFAULT '0',
  `description` varchar(200) DEFAULT NULL,
  `sort_order` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问答板块';

DROP TABLE IF EXISTS `edu_forum_reply`;
CREATE TABLE IF NOT EXISTS `edu_forum_reply` (
  `id` int(6) NOT NULL AUTO_INCREMENT COMMENT '主题回复id',
  `tid` int(6) DEFAULT NULL COMMENT '主题id',
  `uid` int(5) DEFAULT NULL COMMENT '回复用户id',
  `toid` int(6) DEFAULT '0',
  `touid` int(6) DEFAULT '0',
  `content` longtext COMMENT '回复内容',
  `accept` int(2) DEFAULT NULL,
  `zan` int(4) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL COMMENT '回复时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问答回复';

DROP TABLE IF EXISTS `edu_forum_topic`;
CREATE TABLE IF NOT EXISTS `edu_forum_topic` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '论坛主题ID',
  `uid` int(5) DEFAULT NULL COMMENT '作者ID',
  `pid` int(2) DEFAULT NULL COMMENT '板块ID',
  `name` varchar(50) DEFAULT NULL COMMENT '主题标题',
  `content` longtext COMMENT '主题内容',
  `istop` int(2) DEFAULT '0',
  `isessence` int(2) DEFAULT '0',
  `hits` int(8) DEFAULT '0',
  `replays` int(6) DEFAULT '0',
  `knot` int(11) DEFAULT '0',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问答主题';

DROP TABLE IF EXISTS `edu_grade`;
CREATE TABLE IF NOT EXISTS `edu_grade` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `sort_order` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `edu_grade` (`id`, `name`, `sort_order`) VALUES
(1, '小学一年级', 2),
(2, '小学二年级', 3),
(3, '小学三年级', 3),
(5, '小学四年级', 3),
(6, '小学五年级', 0);

DROP TABLE IF EXISTS `edu_jifen`;
CREATE TABLE IF NOT EXISTS `edu_jifen` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `type` varchar(10) NOT NULL,
  `jifen` int(3) NOT NULL,
  `addtime` datetime NOT NULL,
  `times` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分明细';

DROP TABLE IF EXISTS `edu_knowledge`;
CREATE TABLE IF NOT EXISTS `edu_knowledge` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `sectionid` int(5) DEFAULT NULL,
  `title` text,
  `sort_order` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='知识点';

DROP TABLE IF EXISTS `edu_learned`;
CREATE TABLE IF NOT EXISTS `edu_learned` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(6) DEFAULT NULL,
  `cid` int(6) DEFAULT NULL,
  `sid` int(8) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  `duration` int(10) DEFAULT '0',
  `seek` int(5) DEFAULT '0',
  `laststudy` int(8) DEFAULT NULL,
  `status` int(2) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学习记录';

DROP TABLE IF EXISTS `edu_link`;
CREATE TABLE IF NOT EXISTS `edu_link` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `href` varchar(40) DEFAULT NULL,
  `sort` int(2) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='友情链接';

INSERT INTO `edu_link` (`id`, `title`, `href`, `sort`, `status`) VALUES
(1, '云课网校官网', 'https://www.yunknet.cn', 0, 0);

DROP TABLE IF EXISTS `edu_live_course`;
CREATE TABLE IF NOT EXISTS `edu_live_course` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `cid` int(10) NOT NULL,
  `teacher_id` int(10) DEFAULT NULL,
  `material_id` varchar(100) DEFAULT NULL,
  `status` int(2) DEFAULT '0',
  `limit` int(6) DEFAULT '0',
  `is_top` int(2) DEFAULT '0',
  `is_hot` int(2) DEFAULT '0',
  `price` float(8,2) DEFAULT '0.00',
  `youxiaoqi` int(1) DEFAULT '0',
  `picture` varchar(250) DEFAULT NULL,
  `xuni_num` int(10) DEFAULT '0',
  `brief` text,
  `type` int(2) DEFAULT NULL,
  `is_over` int(2) DEFAULT '0',
  `islock` int(2) DEFAULT NULL,
  `iospproductid` varchar(20) DEFAULT NULL,
  `sort_order` int(10) DEFAULT '0',
  `views` int(10) DEFAULT '0',
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播课程';

DROP TABLE IF EXISTS `edu_live_section`;
CREATE TABLE IF NOT EXISTS `edu_live_section` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `csid` int(6) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `isfree` int(2) DEFAULT '0',
  `playtimes` int(2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `max_users` int(5) DEFAULT NULL,
  `ischapter` int(10) DEFAULT NULL,
  `chapterid` int(2) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  `document` text,
  `paperid` int(15) DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `live_type` int(2) DEFAULT NULL,
  `sectype` int(2) DEFAULT NULL,
  `coursetype` int(2) DEFAULT NULL,
  `room_id` varchar(20) DEFAULT NULL,
  `replaplatform` varchar(10) DEFAULT NULL,
  `videoid` varchar(50) DEFAULT NULL,
  `shareurl` varchar(70) DEFAULT NULL,
  `localvideo` varchar(40) DEFAULT NULL,
  `duration` int(4) DEFAULT NULL,
  `sort_order` int(10) DEFAULT '0',
  `status` int(2) DEFAULT '1',
  `previewtimes` int(3) DEFAULT '0',
  `replayurl` text,
  `certificate` varchar(500) DEFAULT NULL,
  `isbulletscreen` int(2) DEFAULT '0',
  `isforward` int(2) DEFAULT '0',
  `timer` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播课时';

DROP TABLE IF EXISTS `edu_marketing`;
CREATE TABLE IF NOT EXISTS `edu_marketing` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '0',
  `type` varchar(10) DEFAULT NULL,
  `c_type` int(2) DEFAULT '0',
  `title` varchar(20) DEFAULT NULL,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='促销活动';

INSERT INTO `edu_marketing` (`id`, `name`, `type`, `c_type`, `title`, `details`) VALUES
(1, 'reg', 'coupon', 0, '首次注册', '{\"numbs\":\"2\",\"rate\":\"6\",\"status\":\"1\"}'),
(2, 'buy', 'coupon', 0, '首次购课', '{\"numbs\":\"3\",\"rate\":\"-1\",\"status\":\"1\"}'),
(6, 'flashsale', 'flashsale', 1, '', '{\"rate\":\"7\",\"starttime\":\"2022-11-19 21:08:18\",\"endtime\":\"2022-11-30 00:00:00\",\"course\":[{\"value\":\"1-21\",\"title\":\"\\u70b9\\u64ad\\u8bfe\\u7a0b\\u6f14\\u793a\",\"disabled\":\"\",\"checked\":\"\"},{\"value\":\"2-15\",\"title\":\"111\",\"disabled\":\"\",\"checked\":\"\"},{\"value\":\"2-14\",\"title\":\"\\u76f4\\u64ad\\u6d4b\\u8bd5\",\"disabled\":\"\",\"checked\":\"\"}]}');

DROP TABLE IF EXISTS `edu_material`;
CREATE TABLE IF NOT EXISTS `edu_material` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '资料ID',
  `uid` int(10) DEFAULT NULL,
  `original_name` varchar(100) DEFAULT NULL COMMENT '原始名称',
  `oss_name` varchar(100) DEFAULT NULL,
  `oss_url` varchar(200) DEFAULT NULL COMMENT 'oss文件路径',
  `type` varchar(20) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程资料';

DROP TABLE IF EXISTS `edu_message`;
CREATE TABLE IF NOT EXISTS `edu_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `uid` int(6) NOT NULL,
  `fromuid` int(6) NOT NULL DEFAULT '0',
  `message` varchar(300) NOT NULL,
  `messagetype` int(2) NOT NULL,
  `messageid` int(6) NOT NULL,
  `addtime` datetime NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_myexam`;
CREATE TABLE IF NOT EXISTS `edu_myexam` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `eid` int(6) DEFAULT NULL COMMENT '试卷ID',
  `pointid` int(6) DEFAULT NULL,
  `uid` int(6) DEFAULT NULL COMMENT '用户ID',
  `cid` int(6) DEFAULT NULL,
  `sid` int(6) DEFAULT NULL,
  `tid` int(2) DEFAULT NULL,
  `authorid` int(5) DEFAULT NULL,
  `ctype` int(2) DEFAULT NULL,
  `myanswer` text COMMENT '我的答案',
  `myscore` text,
  `myresult` text,
  `zgscores` int(3) DEFAULT NULL COMMENT '主观题得分',
  `kgscores` int(3) DEFAULT NULL COMMENT '客观题得分',
  `totalscores` int(3) DEFAULT NULL COMMENT '总分',
  `status` int(2) DEFAULT '0',
  `ispost` int(2) DEFAULT '0',
  `addtime` datetime DEFAULT NULL COMMENT '考试时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我的考试';

DROP TABLE IF EXISTS `edu_myquestions`;
CREATE TABLE IF NOT EXISTS `edu_myquestions` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) DEFAULT NULL,
  `pointid` int(8) DEFAULT NULL,
  `myquestions` text,
  `myerrors` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我做过的试题';

DROP TABLE IF EXISTS `edu_nav`;
CREATE TABLE IF NOT EXISTS `edu_nav` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `pid` int(3) DEFAULT NULL,
  `name` varchar(20) NOT NULL DEFAULT '0',
  `isshow` int(2) DEFAULT NULL,
  `sort_order` int(3) DEFAULT NULL,
  `url` varchar(100) DEFAULT '0',
  `target` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='首页菜单';

INSERT INTO `edu_nav` (`id`, `pid`, `name`, `isshow`, `sort_order`, `url`, `target`) VALUES
(5, 0, '选课', 1, 10, 'javascript:void(0);', '_blank'),
(6, 5, '直播', 1, 10, 'index/course/liveindex', '_blank'),
(7, 5, '点播', 1, 10, 'index/course/videoindex', '_blank'),
(8, 5, '班级', 1, 10, 'index/classroom/index', '_blank'),
(20, 0, '名师', 1, 10, 'index/teacher/index', '_blank'),
(21, 0, '问答', 1, 10, 'index/forum/index', '_blank'),
(22, 0, '资讯', 1, 10, 'index/article/index', '_blank'),
(23, 0, '测评', 1, 10, 'index/exam/index', '_blank'),
(24, 0, '移动端', 1, 10, 'index/index/LiveClient', '_blank'),
(27, 5, '线下', 1, 10, 'index/course/offlinecourse', '_blank');

DROP TABLE IF EXISTS `edu_notes`;
CREATE TABLE IF NOT EXISTS `edu_notes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(6) DEFAULT NULL,
  `cid` int(6) DEFAULT NULL,
  `sid` int(6) DEFAULT NULL,
  `cstype` int(2) DEFAULT NULL,
  `contents` text,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我的笔记';

DROP TABLE IF EXISTS `edu_offline_course`;
CREATE TABLE IF NOT EXISTS `edu_offline_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(5) DEFAULT NULL,
  `title` varchar(250) NOT NULL,
  `teacher_id` int(5) DEFAULT NULL,
  `material_id` int(5) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `is_top` int(2) DEFAULT NULL,
  `is_hot` int(2) DEFAULT NULL,
  `price` float(8,2) DEFAULT NULL,
  `picture` varchar(250) DEFAULT NULL,
  `xuni_num` int(6) DEFAULT NULL,
  `brief` text,
  `sort_order` int(5) DEFAULT '0',
  `views` int(8) DEFAULT '0',
  `type` int(2) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `stulimit` int(5) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_order`;
CREATE TABLE IF NOT EXISTS `edu_order` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) DEFAULT NULL COMMENT '客户ID',
  `cid` int(8) DEFAULT NULL COMMENT '课程ID',
  `tid` int(4) DEFAULT NULL,
  `ctype` int(2) DEFAULT NULL,
  `orderid` varchar(20) DEFAULT NULL COMMENT '本地订单ID',
  `paytype` varchar(10) DEFAULT NULL COMMENT '支付方式',
  `state` int(2) DEFAULT NULL COMMENT '订单状态',
  `addtime` datetime DEFAULT NULL COMMENT '创建时间',
  `total` float(8,2) DEFAULT NULL COMMENT '订单总价',
  `payorder` varchar(40) DEFAULT NULL COMMENT '第三方订单 ID',
  `profit` int(8) DEFAULT '0',
  `duration` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程订单';

DROP TABLE IF EXISTS `edu_other`;
CREATE TABLE IF NOT EXISTS `edu_other` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='教师申请条例';

INSERT INTO `edu_other` (`id`, `type`, `value`) VALUES
(1, 'ordinance', '<p style=\"text-align: center;\"><span style=\"font-size: 20px;\"><strong>教师申请条例</strong></span></p><p>一、课程要求 　 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;1、您的课程必须由您自己原创录制的，不得发布他人的课程，或抄袭，翻版其他人的课程。 　 　　</p><p>&nbsp;&nbsp; &nbsp;2、您应自行负责您课程的开发、创建、上课、管理等工作，本平台会在一定程度上提供助理服务，帮助您更好的开展教学。&nbsp;</p><p>&nbsp;&nbsp;&nbsp;&nbsp;3、您不得在课程内宣传与本课程无关的任何其他信息，包括但不限于广告、其他的课程产品信息等，也不得在课程内添加指向非本平台拥有或控制或本平台书面同意的网站链接。 　 　　</p><p>二、&nbsp;课程运营 　 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;2.1&nbsp;&nbsp;您保证： 　 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（1）您的课程、提供给用户的相关服务及发布的相关信息、内容等，不违反相关法律、法规、政策等的规定及本协议或相关协议、规则等，也不会侵犯任何人的合法权益； 　　</p><p>&nbsp; &nbsp; （2）课程上课过程中应尊重用户知情权、选择权，应当坚持诚信原则，不误导、欺诈、混淆用户，尊重用户的隐私，不骚扰用户，不制造垃圾信息。 　</p><p>&nbsp; &nbsp; 2.2 您不得从事包括但不限于以下行为，也不得为以下行为提供便利： 　 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（1）以任何方式干扰或企图干扰本平台任何产品、任何部分或功能的正常运行，或者制作、发布、传播上述工具、方法等；&nbsp;</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（2）在未经过用户同意的情况下，向任何其他用户及他方显示或以其他任何方式提供该用户的任何信息； 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（3）请求、收集、索取或以其他方式获取用户手机号，本平台服务的登录帐号、密码或其他任何身份验证凭据； 　　 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（4）设置或发布任何违反相关法规、公序良俗、社会公德等的玩法、内容等； 　　</p><p>&nbsp;&nbsp;&nbsp;&nbsp;（5）其他认为不应该、不适当的行为、内容。 　 　　</p>');

DROP TABLE IF EXISTS `edu_page`;
CREATE TABLE IF NOT EXISTS `edu_page` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_profit`;
CREATE TABLE IF NOT EXISTS `edu_profit` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `tid` int(5) DEFAULT NULL,
  `profit` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师分成';

DROP TABLE IF EXISTS `edu_questions`;
CREATE TABLE IF NOT EXISTS `edu_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionchapterid` int(5) DEFAULT NULL,
  `questionknowsid` text,
  `questionuserid` int(11) DEFAULT '0',
  `questiontype` int(2) DEFAULT '0',
  `question` text,
  `questionselect` text,
  `questionanswer` text,
  `questiondescribe` text,
  `questionlastmodifyuser` varchar(120) DEFAULT '',
  `questionselectnumber` tinyint(11) DEFAULT '0',
  `questioncreatetime` datetime DEFAULT NULL,
  `questionstatus` int(1) DEFAULT '1',
  `questionhtml` text,
  `questionparent` int(11) DEFAULT '0',
  `questionsequence` int(3) DEFAULT '0',
  `questionlevel` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `questioncreatetime` (`questioncreatetime`),
  KEY `questiontype` (`questiontype`),
  KEY `questionstatus` (`questionstatus`),
  KEY `questionuserid` (`questionuserid`),
  KEY `questionparent` (`questionparent`,`questionsequence`),
  KEY `questionlevel` (`questionlevel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试题';

DROP TABLE IF EXISTS `edu_question_type`;
CREATE TABLE IF NOT EXISTS `edu_question_type` (
  `id` int(2) NOT NULL AUTO_INCREMENT COMMENT '题型ID',
  `type_name` varchar(20) DEFAULT NULL COMMENT '题型名称',
  `p_type` int(2) DEFAULT NULL COMMENT '展现形式',
  `mark` varchar(20) DEFAULT NULL,
  `sort_order` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='试题类型';

INSERT INTO `edu_question_type` (`id`, `type_name`, `p_type`, `mark`, `sort_order`) VALUES
(1, '单选题', 1, 'SingleSelect', 1),
(2, '多选题', 1, 'MultiSelect', 2),
(3, '填空题', 2, 'FillInBlanks', 3),
(4, '判断题', 1, 'TrueOrfalse', 4),
(5, '简答题', 2, 'ShortAnswer', 5),
(6, '解答题', 2, 'ShortAnswer', 5);

DROP TABLE IF EXISTS `edu_questype`;
CREATE TABLE IF NOT EXISTS `edu_questype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questype` varchar(60) DEFAULT '',
  `questsort` int(1) DEFAULT '0',
  `questchoice` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `questchoice` (`questchoice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试题类型';

DROP TABLE IF EXISTS `edu_regfield`;
CREATE TABLE IF NOT EXISTS `edu_regfield` (
  `id` int(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `field` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `edu_regfield` (`id`, `name`, `status`, `field`) VALUES
(1, '年级', 0, 'greadId'),
(2, '学校', 0, 'schoolId');

DROP TABLE IF EXISTS `edu_school`;
CREATE TABLE IF NOT EXISTS `edu_school` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `sort_order` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='学校数据';

INSERT INTO `edu_school` (`id`, `name`, `sort_order`) VALUES
(2, '邹城实验中学', 1),
(3, '邹城第二中学', 3),
(4, '邹城第一中学', 2);

DROP TABLE IF EXISTS `edu_signup`;
CREATE TABLE IF NOT EXISTS `edu_signup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `cid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `address` varchar(150) NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_system`;
CREATE TABLE IF NOT EXISTS `edu_system` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置' ROW_FORMAT=COMPACT;

INSERT INTO `edu_system` (`name`, `value`) VALUES
('agoraAppid', ''),
('agoraRestfulKey', ''),
('alipay_app_id', ''),
('alipay_public_key', ''),
('alipay_rsa_private_key', ''),
('AliUserId', ''),
('app_appid', ''),
('app_AppSecret', ''),
('app_key', ''),
('app_mch_id', ''),
('author_code', ''),
('author_web', 'https://www.yunknet.cn/'),
('bili', '0.7'),
('Bucket', ''),
('colse_explain', '网站升级中'),
('distributionthree', '0.1'),
('distributiontwo', '0.2'),
('distributioone', '0.4'),
('email_server', 'a:7:{s:4:\"host\";s:0:\"\";s:6:\"secure\";s:3:\"tls\";s:4:\"port\";s:0:\"\";s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:8:\"fromname\";s:0:\"\";s:5:\"email\";s:0:\"\";}'),
('EndPoint', ''),
('isdistributionopen', '1'),
('isvipopen', '1'),
('is_develop', '0'),
('jifen', 'a:13:{s:5:\"login\";s:1:\"1\";s:10:\"logintimes\";s:1:\"1\";s:5:\"tiwen\";s:1:\"3\";s:10:\"tiwentimes\";s:1:\"5\";s:5:\"huida\";s:1:\"1\";s:10:\"huidatimes\";s:1:\"5\";s:5:\"caina\";s:1:\"4\";s:10:\"cainatimes\";s:1:\"3\";s:7:\"taojuan\";s:1:\"5\";s:12:\"taojuantimes\";s:1:\"5\";s:4:\"dian\";s:3:\"0.5\";s:9:\"diantimes\";s:2:\"10\";s:5:\"gouke\";s:3:\"0.1\";}'),
('KeyID', ''),
('KeySecret', ''),
('liveplatform', ''),
('mp_appid', ''),
('mp_AppSecret', ''),
('mp_key', ''),
('mp_mch_id', ''),
('PageSize', '15'),
('page_number', '20'),
('panoAppid', ''),
('panoSecret', ''),
('QQ_Login', 'a:3:{s:9:\"qq_app_id\";s:14:\"QQ扫码登录\";s:10:\"qq_app_key\";s:14:\"QQ扫码登录\";s:4:\"type\";s:8:\"QQ_Login\";}'),
('SmsSign', '云课科技'),
('SmsTemplates_MC', 'a:4:{s:6:\"status\";s:1:\"1\";s:7:\"_verify\";s:1:\"0\";s:4:\"type\";s:15:\"SmsTemplates_MC\";s:11:\"TemplatesId\";s:13:\"SMS_186920072\";}'),
('SmsTemplates_SK', 'a:4:{s:6:\"status\";s:1:\"0\";s:7:\"_verify\";s:1:\"0\";s:4:\"type\";s:15:\"SmsTemplates_SK\";s:11:\"TemplatesId\";s:8:\"模板ID\";}'),
('upload_image', 'a:16:{s:8:\"location\";s:1:\"1\";s:8:\"is_thumb\";s:1:\"0\";s:9:\"max_width\";s:4:\"1200\";s:10:\"max_height\";s:4:\"3600\";s:8:\"is_water\";s:1:\"0\";s:12:\"water_source\";s:0:\"\";s:12:\"water_locate\";s:1:\"1\";s:11:\"water_alpha\";s:2:\"80\";s:7:\"is_text\";s:1:\"0\";s:4:\"text\";s:12:\"知了数学\";s:9:\"text_font\";s:0:\"\";s:11:\"text_locate\";s:1:\"7\";s:9:\"text_size\";s:2:\"20\";s:10:\"text_color\";s:7:\"#000000\";s:11:\"text_offset\";s:2:\"10\";s:10:\"text_angle\";s:2:\"50\";}'),
('version', '3.0.0'),
('vipjiprice', '78'),
('vipnianprice', '298'),
('vipyueprice', '30'),
('website_status', '1'),
('weixin_appid', ''),
('weixin_AppSecret', ''),
('weixin_key', ''),
('Weixin_Login', ''),
('weixin_mch_id', '');

DROP TABLE IF EXISTS `edu_tixian`;
CREATE TABLE IF NOT EXISTS `edu_tixian` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `tid` int(6) DEFAULT NULL,
  `money` int(5) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `account` varchar(30) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师提现';

DROP TABLE IF EXISTS `edu_understand`;
CREATE TABLE IF NOT EXISTS `edu_understand` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `roomid` varchar(20) DEFAULT NULL,
  `understand` int(2) DEFAULT NULL,
  `cishu` int(3) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会了吗数据存储';

DROP TABLE IF EXISTS `edu_user`;
CREATE TABLE IF NOT EXISTS `edu_user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` varchar(40) DEFAULT '0',
  `admin_id` int(5) DEFAULT NULL,
  `is_teacher` int(2) DEFAULT NULL,
  `category_id` varchar(20) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL COMMENT '用户名',
  `nickname` varchar(20) DEFAULT NULL,
  `sex` int(2) DEFAULT NULL,
  `avatar` varchar(200) DEFAULT '0',
  `mobile` char(20) DEFAULT '' COMMENT '手机',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `status` tinyint(1) DEFAULT '1' COMMENT '0禁用/1启动',
  `last_login_time` int(10) UNSIGNED DEFAULT '0' COMMENT '上次登录时间',
  `last_login_ip` varchar(16) DEFAULT '' COMMENT '上次登录IP',
  `login_count` int(11) DEFAULT '0' COMMENT '登录次数',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT '0' COMMENT '更新时间',
  `safewords` varchar(20) DEFAULT NULL,
  `yue` float(8,2) DEFAULT '0.00',
  `forumadmin` int(2) DEFAULT '0',
  `greadId` int(3) DEFAULT NULL,
  `schoolId` int(3) DEFAULT NULL,
  `qqopenid` varchar(50) DEFAULT NULL,
  `jifen` int(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员' ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `edu_user_classroom`;
CREATE TABLE IF NOT EXISTS `edu_user_classroom` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `classroomid` int(6) DEFAULT '0',
  `uid` int(6) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我的班级';

DROP TABLE IF EXISTS `edu_user_course`;
CREATE TABLE IF NOT EXISTS `edu_user_course` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) DEFAULT NULL,
  `cid` int(8) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  `state` int(2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我的课程';

DROP TABLE IF EXISTS `edu_user_log`;
CREATE TABLE IF NOT EXISTS `edu_user_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员id',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求链接',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '请求类型',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '资源类型',
  `param` text NOT NULL COMMENT '请求参数',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员日志' ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `edu_video_course`;
CREATE TABLE IF NOT EXISTS `edu_video_course` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `cid` int(10) DEFAULT NULL,
  `teacher_id` int(10) DEFAULT NULL,
  `material_id` varchar(100) DEFAULT NULL,
  `status` int(2) DEFAULT '1',
  `is_top` int(2) DEFAULT '0',
  `is_hot` int(2) DEFAULT '0',
  `price` float(8,2) DEFAULT '0.00',
  `picture` varchar(250) DEFAULT NULL,
  `xuni_num` int(10) DEFAULT NULL,
  `brief` text,
  `is_over` int(2) DEFAULT '0',
  `youxiaoqi` int(5) DEFAULT '0',
  `sort_order` int(10) DEFAULT '0',
  `views` int(10) DEFAULT '0',
  `type` int(2) DEFAULT NULL,
  `islock` int(2) NOT NULL DEFAULT '0',
  `iospproductid` varchar(20) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='点播课程';

DROP TABLE IF EXISTS `edu_video_section`;
CREATE TABLE IF NOT EXISTS `edu_video_section` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `csid` int(6) NOT NULL,
  `title` varchar(50) NOT NULL,
  `isfree` int(2) DEFAULT '0',
  `isforward` int(2) DEFAULT NULL,
  `playtimes` varchar(20) DEFAULT NULL,
  `previewtimes` int(5) DEFAULT '0',
  `isbulletscreen` int(2) DEFAULT '0',
  `videoid` varchar(40) DEFAULT NULL,
  `duration` varchar(8) DEFAULT NULL,
  `document` longtext COMMENT '文本课程',
  `ischapter` int(10) DEFAULT NULL,
  `sectype` int(2) DEFAULT NULL COMMENT '1视频课程，2语音课程，3文本课程，4练习题',
  `coursetype` int(2) DEFAULT NULL,
  `chapterid` int(2) DEFAULT '0',
  `paperid` int(15) DEFAULT NULL,
  `sort_order` int(10) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `platform` varchar(20) DEFAULT '',
  `localvideo` varchar(300) DEFAULT '',
  `sharevideo` varchar(400) DEFAULT '',
  `certificate` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='点播课时';

DROP TABLE IF EXISTS `edu_vip`;
CREATE TABLE IF NOT EXISTS `edu_vip` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL,
  `endtime` datetime NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `edu_vipset`;
CREATE TABLE IF NOT EXISTS `edu_vipset` (
  `type` varchar(10) NOT NULL,
  `iospproductid` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `edu_vipset` (`type`, `iospproductid`) VALUES
('ji', 'VIP2'),
('nian', 'VIP3'),
('yue', 'VIP1');

DROP TABLE IF EXISTS `edu_virtualcomment`;
CREATE TABLE IF NOT EXISTS `edu_virtualcomment` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `uid` int(5) NOT NULL,
  `comment` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
