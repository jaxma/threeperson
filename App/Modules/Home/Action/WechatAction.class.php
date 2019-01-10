<?php

class WechatAction extends Action {

    private $wechat_obj;

    /**
     * 构造函数
     * * */
    public function __construct() {
        parent::__construct();
        $options = array(
            'token' => C('APP_TOKEN'), //填写您设定的key
            'encodingaeskey' => C('APP_AESK'), //填写加密用的EncodingAESKey，如接口为明文模式可忽略
            'appid' => C('APP_ID'), //填写高级调用功能的app id
            'appsecret' => C('APP_SECRET'), //填写高级调用功能的密钥
        );
        import("Wechat.Wechat", APP_PATH);
        $this->wechat_obj = new Wechat($options);
    }
    
    //入口
    public function index() {
        if (!isset($_GET['echostr'])) {
            //$wechat->responseMsg();
            //$postObj = $this->wechat_obj->getRev_obj();
            $type = $this->wechat_obj->getRev()->getRevType();
            //消息类型分离
            switch ($type) {
                case Wechat::MSGTYPE_EVENT:
                    $result = $this->receiveEvent();
                    break;
                default:
                    $result = "unknown msg type: " . $type;
                    break;
            }
            echo $result;
        } else {
            $this->wechat_obj->valid(); //明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        }
    }
    
    
    /**
     * 调用授权跳转接口
     * * */
    public function authJump() {
        $action = I('get.action');
        $ct = I('get.ct');
        $level = I('level');
        $return_url = I('return_url');
        $app_test = C('APP_TEST');
        
        $callback = "http://" . C('YM_DOMAIN') . "/index.php/Home/Wechat/getUserInfo?action=" . $action . "&ct=" . $ct."&level=".$level.'&return_url='.$return_url;
        
        if( $app_test ){
            $url = $callback;
        }
        else{
            $state = '1';
            $url = $this->wechat_obj->getOauthRedirect($callback, $state);
            ob_end_clean();
        }
        
        
        header("location:$url");
    }

    /**
     * 获取微信用户基本信息(未关注也可获取)
     * * */
    public function getUserInfo() {
        $action = I('get.action');
        $ct = I('get.ct');
        $level = I('level');
        $url = I('return_url');
        $app_test = C('APP_TEST');
        
        $url = !empty($url)?base64_decode($url):'';
        
        if( $app_test ){
            $openid = 'notopenid-'.microtime().rand(0, 99999999);
//            $openid = 'oOtsnwcO4vf_m-Ak3irINA37JWxU';
            
            $WechatUserInfo = [
                'openid'    =>  $openid,
                'nickname'  =>  'topos',
                'sex'       =>  1,
                'language'  =>  'zh_CN',
                'city'      =>  'Guangzhou',
                'province'  =>  'Guangdong',
                'country'   =>  'CN',
                'headimgurl'=>  __PUBLIC__.'/Admin_v2/images/headimg.png',
                'privilege' =>  [],
            ];
            $userdetail = [
                'subscribe' =>  1,
                'openid'    =>  $openid,
                'nickname'  =>  'topos',
                'sex'       =>  '1',
                'language'  =>  'zh_CN',
                'city'      =>  '广州',
                'province'  =>  '广东',
                'country'   =>  '中国',
                'headimgurl'=>  __PUBLIC__.'/Admin_v2/images/headimg.png',
                'subscribe_time'    =>  '1501470345',
                'remark'    =>  '',
                'groupid'   =>  0,
                'tagid_list'    =>  [],
            ];
            
            $OauthAccessToken['openid'] = $openid;
        }
        else{
            //$WechatUserInfo使用的access_token是特殊的授权access_token，下面用户信息用的是普通的access_token
//            $OauthAccessToken = $this->wechat_obj->getOauthAccessToken();
//            $WechatUserInfo = $this->wechat_obj->getOauthUserinfo($OauthAccessToken['access_token'], $OauthAccessToken['openid']);
//
//            if( $action == 'getsaleinfo' ){
//                $access_token = $this->wechat_obj->checkAuth();
//                $userdetail = $this->wechat_obj->getUserInfo($OauthAccessToken['openid'],true);
//            }
            
            //$WechatUserInfo用的是特殊的access_token，用户信息用的是普通的access_token
            $OauthAccessToken = $this->wechat_obj->getOauthAccessToken();
            $OauthAccessToken_error['result'] = $OauthAccessToken;
            $OauthAccessToken_error['errCode'] = $this->wechat_obj->errCode;
            $OauthAccessToken_error['errMsg'] = $this->wechat_obj->errMsg;

            $WechatUserInfo = $this->wechat_obj->getOauthUserinfo($OauthAccessToken['access_token'], $OauthAccessToken['openid']);

            $openid = !empty($OauthAccessToken['openid'])?$OauthAccessToken['openid']:$_SESSION['oid'];

            if( $action == 'getsaleinfo' || $action == 'getinfo' || $action=='getsalesignup' ){
                $access_token = $this->wechat_obj->checkAuth();
                $userdetail = $this->wechat_obj->getUserInfo($openid,true);
            }
            
        }
        
        
        
//        $get_wechart_num = isset($_SESSION['get_wechart_num'])?$_SESSION['get_wechart_num']:0;
//        
//        $get_wechart_num++;
//        session('get_wechart_num', $get_wechart_num);
//        if( $get_wechart_num > 1 && empty($WechatUserInfo) ){
//            echo '<h1>ERROR WECHAT WAY</h1>';
//            session('get_wechart_num', 0);
//            return;
//        }
        
        
        if (!empty($WechatUserInfo['openid'])) {
            //session('[start]');//thinkPHP启动session
            switch ($action) {
                case 'applyAllAgent':
                    session('login', 'yes');
                    session('oid', $WechatUserInfo['openid']);
                    session('headimgurl', $WechatUserInfo['headimgurl']);
                    session('nickname', $WechatUserInfo['nickname']);
                    $this->redirect('Home/Manager/applyAllAgent?level='.$level);
                    break;
                
                case 'index':
                    session('login', 'yes');
                    session('logina', 'yes');
                    session('oid', $WechatUserInfo['openid']);
                    session('headimgurl', $WechatUserInfo['headimgurl']);
                    session('nickname', $WechatUserInfo['nickname']);
                    session('wechatinfo',$WechatUserInfo);
                    
                    if( !empty($url) ){
                        $this->redirect($url);
                    }
                    else{
                        $this->redirect('Admin/Index/index');
                    }
                    break;
                
                case 'apply':
                    session('login', 'yes');
                    session('oid', $WechatUserInfo['openid']);
                    session('headimgurl', $WechatUserInfo['headimgurl']);
                    session('nickname', $WechatUserInfo['nickname']);
                    session('sex', $WechatUserInfo['sex']);
                    if( empty($url) ){
                        $url = "http://" . C('YM_DOMAIN') . "/index.php/Admin/Development/apply?ct=" . $ct;
                    }
                    echo "<script>location.href='$url'</script>";
                    break;
                case 'getsaleinfo':
                    if( !isset($userdetail['subscribe']) ){
                        setLog('wechat:$userdetail-'.var_export($userdetail,1).',$access_token-'.$access_token.',$WechatUserInfo-'.var_export($WechatUserInfo,1).',$OauthAccessToken_error-'.var_export($OauthAccessToken_error,1).',$openid-'.$openid,'wechat_error');
                    }
                    if( !$OauthAccessToken || empty($userdetail) ){
//                        print_r($OauthAccessToken);echo '<hr />';
//                        print_r($userdetail);
//                        return;
                        
                        $url = __GROUP__.'/index';
                        echo "<script>";
                        echo "location.href='$url'";
                        echo "</script>";
                        return;
                    }
                    
                    $WechatUserInfo['subscribe'] = isset($userdetail['subscribe'])?$userdetail['subscribe']:'0';
                    session('sale_openid', $userdetail['openid']);
                    session('oid', $WechatUserInfo['openid']);
                    session('userdetail', $userdetail);
                    session('wechatinfo',$WechatUserInfo);
//                    echo '<pre>';var_dump($OauthAccessToken);echo '</pre>';
//                    echo '<pre>';var_dump($WechatUserInfo);echo '</pre>';
//                    echo '<pre>';var_dump($userdetail);echo '</pre>';
                    $this->redirect($url);
                    break;
                case 'getsalesignup':
                    if( !$OauthAccessToken || empty($userdetail) ){
//                        print_r($OauthAccessToken);echo '<hr />';
//                        print_r($userdetail);
//                        return;
                        
                        $url = __GROUP__.'/index';
                        echo "<script>";
                        echo "location.href='$url'";
                        echo "</script>";
                        return;
                    }
                    
                    session('usersignup', $userdetail);
                    session('wechatsignup',$WechatUserInfo);
//                    echo '<pre>';var_dump($OauthAccessToken);echo '</pre>';
//                    echo '<pre>';var_dump($WechatUserInfo);echo '</pre>';
//                    echo '<pre>';var_dump($userdetail);echo '</pre>';
                    $this->redirect($url);
                    break;
                case 'getinfo':
                    if( empty($userdetail) ){
                        $content = '获取微信信息失败，请重试！';
                        $return_url = __APP__.'/admin/index';
                        error_tip($content, '', $return_url);
                        return;
                    }
                    
                    session('userinfo',$userdetail);
                    if( empty($url) ){
                        $url = "http://" . C('YM_DOMAIN') . "/Admin/index";
                    }
                    echo "<script>location.href='$url'</script>";
                    break;
                default:
                    break;
            }
        }
        else{
        	$this->error('获取微信信息有误，请重试','/home/index');
        }
    }
    
    

    /**
     * +-------------------------------
     * 事件处理
     * +-------------------------------
     */
    public function receiveEvent() {
        $EventArray = $this->wechat_obj->getRevEvent();
        switch ($EventArray['event']) {
            case Wechat::EVENT_SUBSCRIBE:
                $content = "欢迎关注微商管理系统。";
                break;
            case Wechat::EVENT_UNSUBSCRIBE:
                $content = "取消关注";
                break;
            case Wechat::EVENT_SCAN:
                $content = "扫描场景 " . $EventArray['key'];
                break;
            case Wechat::EVENT_MENU_CLICK:
                switch ($EventArray['key']) {
                    case "COMPANY":
                        $content = array();
                        $content[] = array(
                            "Title" => "多图文1标题",
                            "Description" => "",
                            "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
                        );
                        break;
                    default:
                        $content = "点击菜单：" . $EventArray['key'];
                        break;
                }
                break;
            case Wechat::EVENT_LOCATION:
                $LocationArray = $this->wechat_obj->getRevEventGeo();
                $content = "上传位置：纬度 " . $LocationArray['x'] . ";经度 " . $LocationArray['y'];
                break;
            case Wechat::EVENT_MENU_VIEW:
                $content = "跳转链接 " . $EventArray['key'];
                break;
            case Wechat::EVENT_SEND_MASS:
                $SEND_MASS_Array = $this->wechat_obj->getRevResult();
                $content = "消息ID：" . $SEND_MASS_Array['MsgID'] . "，结果：" . $SEND_MASS_Array['Status'] . "，粉丝数："
                        . $SEND_MASS_Array['TotalCount'] . "，过滤：" . $SEND_MASS_Array['FilterCount']
                        . "，发送成功：" . $SEND_MASS_Array['SentCount'] . "，发送失败：" . $SEND_MASS_Array['ErrorCount'];
                break;
            default:
                $content = "receive a new event: " . $EventArray['event'];
                break;
        }
        if (is_array($content)) {
            if (isset($content[0])) {
                $this->wechat_obj->news($content)->reply();
            } else if (isset($content['MusicUrl'])) {
                $this->wechat_obj->music($content)->reply();
            }
        } else {
            $this->wechat_obj->text($content)->reply();
        }
    }

    /**
     * +-----------------------------
     * 回复文本
     * +-----------------------------
     */
    public function receiveText() {
        $keyword = trim($this->wechat_obj->getRevContent());
        //多客服人工回复模式
        if (strstr($keyword, "您好") || strstr($keyword, "您好") || strstr($keyword, "在吗")) {
            $result = $this->wechat_obj->transfer_customer_service()->reply();
        }
        //自动回复模式
        else {
            if (strstr($keyword, "文本")) {
                $content = "这是个文本消息";
            } else if (strstr($keyword, "单图文")) {
                $content = array();
                $content[] = array(
                    "Title" => "单图文标题",
                    "Description" => "单图文内容",
                    "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
                );
            } else if (strstr($keyword, "图文") || strstr($keyword, "多图文")) {
                $content = array();
                $content[] = array(
                    "Title" => "多图文1标题",
                    "Description" => "",
                    "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
                );
                $content[] = array(
                    "Title" => "多图文2标题",
                    "Description" => "",
                    "PicUrl" => "http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg",
                );
                $content[] = array(
                    "Title" => "多图文3标题",
                    "Description" => "",
                    "PicUrl" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg",
                );
            } else if (strstr($keyword, "音乐")) {
                $content = array();
                $content = array(
                    "Title" => "最炫民族风",
                    "Description" => "歌手：凤凰传奇",
                );
            } else {
                $FromUserName = $this->wechat_obj->getRevFrom();
                $content = date("Y-m-d H:i:s", time()) . "\n" . $FromUserName['FromUserName'] . "\n技术支持 By Eiffel";
            }

            if (is_array($content)) {
                if (isset($content[0]['PicUrl'])) {
                    $this->wechat_obj->news($content)->reply();
                } else if (isset($content['MusicUrl'])) {
                    $this->wechat_obj->music($content)->reply();
                }
            } else {
                $this->wechat_obj->text($content)->reply();
            }
        }
    }

    public function getMedia_id() {
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=K2QyhY-zWQYNuwZLTiVgn6MLxbbRgkTImZS7VkP4GPX-xFFn8c6qpYELDD626qyDftVfsUwB9t3wyXKYfy9J99QQ7Ae4RH6AftXqrGgzRAMXJKhAHARDE";
        $data = array(
            "type" => "image",
            "offset" => 0,
            "count" => 20
        );
        $data = json_encode($data);
        $res = https_request($url, $data);
        var_dump($res);
    }

    /**
     * +----------------------------------
     * 设置自定义菜单
     * +----------------------------------
     */
    public function setMenu() {
        $wzyUrl = "http://" . C('YM_DOMAIN') . "/home";
        $gwUrl = "http://web72-16003.17.xiniu.com/index.aspx";
        $fwcxUrl = "http://" . C('YM_DOMAIN') . "/home/security";
        //$ooo = "#";
        $newmenu = array(
            'button' => array(
                0 => array(
                    'name' => '关于公司',
                    'sub_button' => array(
                        0 => array(
                            'type' => 'view',
                            'name' => '微官网',
                            'url' => "$wzyUrl",
                        ),
                        1 => array(
                            'type' => 'view',
                            'name' => '宣传图',
                            'url' => "http://" . C('YM_DOMAIN') . "/home/taobao",
                        ),
                        2 => array(
                            'type' => 'view',
                            'name' => '公司资质',
                            'url' => "http://" . C('YM_DOMAIN') . "/home/company",
                        ),
                    ),
                ),
                1 => array(
                    'name' => '经销商后台',
                    'type' => 'view',
                    'url' => "http://" . C('YM_DOMAIN') . "/admin",
                ),
                2 => array(
                    'name' => '防伪查询',
                    'type' => 'view',
                    'url' => "$fwcxUrl",
                ),
            ),
        );
        $result = $this->wechat_obj->createMenu($newmenu);
        if ($result) {
            echo "success!";
        } else {
            echo "error!";
        }
    }

    //接收图片消息
    private function receiveImage($object) {
        $PicArray = $this->wechat_obj->getRevPic();
        $this->wechat_obj->image($PicArray['mediaid'])->reply();
    }

    //接收位置消息
    private function receiveLocation() {
        $LocationArray = $this->wechat_obj->getRevGeo();
        $content = "您发送的是位置，纬度为：" . $LocationArray['x'] . "；经度为：" . $LocationArray['y'] . "；缩放级别为："
                . $LocationArray['scale'] . "；位置为：" . $LocationArray['label'];
        $this->wechat_obj->text($content)->reply();
    }

    //接收语音消息
    private function receiveVoice() {
        $VoiceArray = $this->wechat_obj->getRevContent();
        if (isset($VoiceArray['Recognition']) && !empty($VoiceArray['Recognition'])) {
            $content = "您刚才说的是：" . $VoiceArray;
            $this->wechat_obj->text($content)->reply();
        } else {
            $content = $VoiceArray['mediaid'];
            $this->wechat_obj->voice($content)->reply();
        }
    }

    //接收视频消息
    private function receiveVideo() {
        $VideoArray = $this->wechat_obj->getRevVideo();
        $content = array(
            "MediaId" => $VideoArray['mediaid'],
            "ThumbMediaId" => $VideoArray['thumbmediaid'],
            "Title" => "精彩片段",
            "Description" => "精彩内容"
        );
        $this->wechat_obj->video($content['MediaId'], $content['Title'], $content['Description'])->reply();
    }

    //接收链接消息
    private function receiveLink() {
        $LinkArray = $this->wechat_obj->getRevLink();
        $content = "您发送的是链接，标题为：" . $LinkArray['Title'] . "；内容为：" . $LinkArray['Description'] . "；链接地址为：" . $LinkArray['Url'];
        $this->wechat_obj->text($content)->reply();
    }

    //回复多客服消息
    private function transmitService($object) {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
           </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

}

?>