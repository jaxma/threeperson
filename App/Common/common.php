<?php

//检测是否登陆
function checklogin() {
    if (!isset($_SESSION['aname'])) {
        $this->error('没有登录', '__APP__/Radmin/Login/index');
    }
}

    //解码share
function decode_share_link($share){
        
        $decode_share = tiriDecode($share);
        $decode_share = unserialize($decode_share);
            
        return $decode_share;
    
    }


//检测微信网页授权
function checkAuth($action, $ct = "",$level="",$return_url="") {
    $app_test = C('APP_TEST');
    
    if ( $app_test ) {
//        return;
    }
    
    if( !empty($return_url) ){
        $return_url = base64_encode($return_url);
    }
    
    
    if ($action == 'index') {
        if( $app_test ){
            $m_url = __APP__ . "/Home/Wechat/getUserInfo?action=" . $action.'&return_url='.$return_url;
        }
        else{
            $m_url = __APP__ . "/Home/Wechat/authJump?action=" . $action.'&return_url='.$return_url;
        }
        header("location:$m_url");
        exit();
    }
    elseif ($action == 'getinfo') {
        $m_url = __APP__ . "/Home/Wechat/authJump?action=" . $action.'&return_url='.$return_url;
        header("location:$m_url");
        exit();
    }
    elseif (session('login') != 'yes') {
        $m_url = __APP__ . "/Home/Wechat/authJump?action=" . $action . "&ct=" . $ct.'&level='.$level.'&return_url='.$return_url;
        header("location:$m_url");
        exit();
    }
    elseif( $action == 'getsaleinfo' ){
        if( $app_test ){
            $m_url = __APP__ . "/Home/Wechat/getUserInfo?action=" . $action.'&return_url='.$return_url;
        }
        else{
            $m_url = __APP__ . "/Home/Wechat/authJump?action=" . $action.'&return_url='.$return_url;
        }    
        
        header("location:$m_url");
        exit();
    }
    else{
        
        if( $app_test ){
            $m_url = __APP__ . "/Home/Wechat/getUserInfo?action=" . $action . "&ct=" . $ct.'&level='.$level.'&return_url='.$return_url;
        }
        else{
            $m_url = __APP__ . "/Home/Wechat/authJump?action=" . $action . "&ct=" . $ct.'&level='.$level.'&return_url='.$return_url;
        }
        
        
        header("location:$m_url");
        exit();
    }
    
}

//数组打印函数
function p($arr) {
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function toValid($m_temp) {
    return addslashes(htmlspecialchars(trim($m_temp)));
}

function setLog($m_array, $prex = "") {
    import('Class.Logs', APP_PATH);
    $dir = "log/" . date("Y/m", time());
    if (!empty($prex)) {
        $filename = $prex . "-" . date("d", time()) . ".log";
    } else {
        $filename = date("d", time()) . ".log";
    }
    $logs = new Logs("", $dir, $filename);
    $logs->setlog($m_array);
}

function image_save($file, $path) {
    if ($file["type"] == "image/gif") {
        @$im = imagecreatefromgif($file['tmp_name']);
        if ($im) {
            $sign = imagegif($im, $path);
        } else {
            return "error";
        }
    } elseif ($file["type"] == "image/png" || $file["type"] == "image/x-png") {
        @$im = imagecreatefrompng($file['tmp_name']);
        if ($im) {
            $sign = imagepng($im, $path);
        } else {
            return "error";
        }
    } else {
        @$im = imagecreatefromjpeg($file['tmp_name']);
        if ($im) {
            $sign = imagejpeg($im, $path, 100);
        } else {
            return "error";
        }
    }
    return $sign;
}

function delEditorImage($m_string, $find_str) {
    $m_count = substr_count($m_string, "editor/");
    for ($i = 0; $i < $m_count; $i ++) {
        $m_tempstr = stristr($m_string, "editor/");
        $m_pos = stripos($m_tempstr, "alt");
        $m_image_src = substr($m_tempstr, 0, $m_pos);
        $m_pos -= 2;
        $m_tempimg = substr($m_tempstr, 0, $m_pos);
        $del_path = "public/Admin/" . $m_tempimg;
        $del_num = 0;
        clearstatcache();
        if (is_file($del_path)) {
            $del_res = unlink($del_path);
            ++$del_num;
        } else {
            $del_num = 0;
        }
        $m_string = str_ireplace($m_image_src, "", $m_string);
    }
    return $del_num;
}

function delUploadImage($m_upload_imagePath) {
    clearstatcache();
    if (is_file($m_upload_imagePath)) {
        $del_res = unlink($m_upload_imagePath);
        if ($del_res == false) {
            return error;
        } else {
            return success;
        }
    }
}

function https_request($url, $data = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
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
* curl抓取数据
*
* @param string $url
* @param string $data
* @param strnig $method
* @return bool/string
*/
function curl_snatch($url,$data,$method='GET')
{
   if( function_exists('curl_init') ){
       $ch = curl_init();

       if( $method=='GET'){
           $url = $url.'?'.$data;
       }
       curl_setopt($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_VERBOSE, 1);

       curl_setopt($ch, CURLOPT_SSLVERSION, 3);

       //turning off the server and peer verification(TrustManager Concept).
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

       curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
       if( $method=='POST' ){
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
       }

       $response = curl_exec($ch);
       curl_close($ch);

       return $response;
   }

   return FALSE;
}//end func curl_snatch

//判断有没有审核
function toStatus($status) {
    if ($status == 0 || $status == 4) {
        return '<span class="label label-danger">未审核</span>';
    } else if ($status == 1) {
        return '已审核';
    } else if ($status == 2) {
        return '<span class="label label-warning">待总部审核</span>';
    }
}

function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date($format, $time);
}

function dateLimit($format = 'Y-m-d', $time) {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    $time = $time + 3600 * 24 * 365;
    return date($format, $time);
}

function toName($templet_id) {
    $name = D('Templet')->where(array('id' => $templet_id))->getField('name');
    return $name;
}

function toAgent($agent_id) {
    $agent_name = M('Distributor')->field('name,wechatnum')->where(array('id' => $agent_id))->find();
    $agent = $agent_name['name']; //."<br>".$agent_name['wechatnum'];
    return $agent;
}

function uploadImg($name, $url) {

    import('ORG.Net.UploadFile');
    $upload = new UploadFile(); // 实例化上传类
    $upload->maxSize = 3145728; // 设置附件上传大小
    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
    $upload->savePath = $url; // 设置附件上传目录
    $upload->saveRule = time() . '_' . mt_rand();
    $imgUrl = $upload->savePath . $upload->saveRule;
    $imgUrl = ltrim($imgUrl, '.');
    if (!$upload->uploadOne($name, $url)) {// 上传错误提示错误信息
        $this->error($upload->getErrorMsg());
    } else {// 上传成功
        return $imgUrl;
    }
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String
 */
function decode($string = '', $skey = 'cxphp') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

function toHide($idennum) {
    if (18 == strlen($idennum)) {
        $idennum = substr($idennum, 0, 6) . '********' . substr($idennum, -4);
        return $idennum;
    }
    return $idennum;
}

//获取jsapi_ticket
function get_jsapi_ticket() {
    import('Wechat.Jssdk', APP_PATH);
    $jssdk = new Jssdk(C('APP_ID'), C('APP_SECRET'));
    return $jssdk->GetSignPackage();
}

//获取access_token
function get_access_token() {
    import('Wechat.Jssdk', APP_PATH);
    $jssdk = new Jssdk(C('APP_ID'), C('APP_SECRET'));
    return $jssdk->getAccessToken();
}

//    function get_test(){
//        return 123;
//    }


function get_ip() { 
    if (getenv('HTTP_CLIENT_IP')) { 
        $ip = getenv('HTTP_CLIENT_IP'); 
    } 
    elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_X_FORWARDED_FOR'); 
    } 
    elseif (getenv('HTTP_X_FORWARDED')) { 
        $ip = getenv('HTTP_X_FORWARDED'); 
    } 
    elseif (getenv('HTTP_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_FORWARDED_FOR'); 

    } 
    elseif (getenv('HTTP_FORWARDED')) { 
        $ip = getenv('HTTP_FORWARDED'); 
    } 
    else { 
        $ip = $_SERVER['REMOTE_ADDR']; 
    } 
    return $ip; 
} 


//获取快递100的快递公司代码
function AllShipperCode(){
        
        $code = array(
            "aae" => "aae全球专递",
            "anjie" => "安捷快递",
            "anxindakuaixi" => "安信达快递",
            "annengwuliu"   =>  "安能物流",
            "biaojikuaidi" => "彪记快递",
            "bht" => "bht",
            "baifudongfang" => "百福东方国际物流",
            "coe" => "中国东方（COE）",
            "changyuwuliu" => "长宇物流",
            "datianwuliu" => "大田物流",
            "debangwuliu" => "德邦物流",
            "dhl" => "dhl",
            "dpex" => "dpex",
            "dsukuaidi" => "d速快递",
            "disifang" => "递四方",
            "ems" => "ems快递",
            "fedex" => "fedex（国外）",
            "feikangda" => "飞康达物流",
            "fenghuangkuaidi" => "凤凰快递",
            "feikuaida" => "飞快达",
            "guotongkuaidi" => "国通快递",
            "ganzhongnengda" => "港中能达物流",
            "guangdongyouzhengwuliu" => "广东邮政物流",
            "gongsuda" => "共速达",
            "huitongkuaidi" => "汇通快运",
            "hengluwuliu" => "恒路物流",
            "huaxialongwuliu" => "华夏龙物流",
            "haihongwangsong" => "海红",
            "haiwaihuanqiu" => "海外环球",
            "jiayiwuliu" => "佳怡物流",
            "jinguangsudikuaijian" => "京广速递",
            "jixianda" => "急先达",
            "jjwl" => "佳吉物流",
            "jymwl" => "加运美物流",
            "jindawuliu" => "金大物流",
            "jialidatong" => "嘉里大通",
            "jykd" => "晋越快递",
            "kuaijiesudi" => "快捷速递",
            "lianb" => "联邦快递（国内）",
            "lianhaowuliu" => "联昊通物流",
            "longbanwuliu" => "龙邦物流",
            "lijisong" => "立即送",
            "lejiedi" => "乐捷递",
            "minghangkuaidi" => "民航快递",
            "meiguokuaidi" => "美国快递",
            "menduimen" => "门对门",
            "ocs" => "OCS",
            "peisihuoyunkuaidi" => "配思货运",
            "quanchenkuaidi" => "全晨快递",
            "quanfengkuaidi" => "全峰快递",
            "quanjitong" => "全际通物流",
            "quanritongkuaidi" => "全日通快递",
            "quanyikuaidi" => "全一快递",
            "rufengda" => "如风达",
            "santaisudi" => "三态速递",
            "shenghuiwuliu" => "盛辉物流",
            "shentong" => "申通",
            "shunfeng" => "顺丰",
            "sue" => "速尔物流",
            "shengfeng" => "盛丰物流",
            "saiaodi" => "赛澳递",
            "tiandihuayu" => "天地华宇",
            "tiantian" => "天天快递",
            "tnt" => "tnt",
            "ups" => "ups",
            "wanjiawuliu" => "万家物流",
            "wenjiesudi" => "文捷航空速递",
            "wuyuan" => "伍圆",
            "wxwl" => "万象物流",
            "xinbangwuliu" => "新邦物流",
            "xinfengwuliu" => "信丰物流",
            "yafengsudi" => "亚风速递",
            "yibangwuliu" => "一邦速递",
            "youshuwuliu" => "优速物流",
            "youzhengguonei" => "邮政包裹挂号信",
            "youzhengguoji" => "邮政国际包裹挂号信",
            "yuanchengwuliu" => "远成物流",
            "yuantong" => "圆通速递",
            "yuanweifeng" => "源伟丰快递",
            "yuanzhijiecheng" => "元智捷诚快递",
            "yunda" => "韵达快运",
            "yuntongkuaidi" => "运通快递",
            "yuefengwuliu" => "越丰物流",
            "yad" => "源安达",
            "yinjiesudi" => "银捷速递",
            "zhaijisong" => "宅急送",
            "zhongtiekuaiyun" => "中铁快运",
            "zhongtong" => "中通速递",
            "zhongyouwuliu" => "中邮物流",
            "zhongxinda" => "忠信达",
            "zhimakaimen" => "芝麻开门",
        );
        
        return $code;
}//end func AllShipperCode


//快递鸟支持的快递
function kdn_code(){
    $all_code = Array
        (
            "SF" => "顺丰",
            "HTKY" => "百世快递",
            "ZTO" => "中通",
            "STO" => "申通",
            "YTO" => "圆通",
            "YD" => "韵达",
            "YZPY" => "邮政平邮",
            "EMS" => "EMS",
            "HHTT" => "天天",
            "JD" => "京东",
            "QFKD" => "全峰",
            "GTO" => "国通",
            "UC" => "优速",
            "DBL" => "德邦",
            "FAST" => "快捷",
            "AMAZON" => "亚马逊",
            "ZJS" => "宅急送",
            "AJ" => "安捷快递",
            "AMAZON" => "亚马逊物流",
            "ANE" => "安能物流",
            "AXD" => "安信达快递",
            "AYCA" => "澳邮专线",
            "BQXHM" => "北青小红帽",
            "BFDF" => "百福东方",
            "BTWL" => "百世快运",
            "CCES" => "CCES快递",
            "CITY100" => "城市100",
            "CJKD" => "城际快递",
            "CNPEX" => "CNPEX中邮快递",
            "COE" => "COE东方快递",
            "CSCY" => "长沙创一",
            "CDSTKY" => "成都善途速运",
            "CTG" => "联合运通",
            "DSWL" => "D速物流",
            "DTWL" => "大田物流",
            "FAST" => "快捷速递",
            "FEDEX" => "FEDEX联邦(国内件）",
            "FEDEX_GJ" => "FEDEX联邦(国际件）",
            "FKD" => "飞康达",
            "GDEMS" => "广东邮政",
            "GSD" => "共速达",
            "GTO" => "国通快递",
            "GTSD" => "高铁速递",
            "HFWL" => "汇丰物流",
            "HHTT" => "天天快递",
            "HLWL" => "恒路物流",
            "HOAU" => "天地华宇",
            "HOTSCM" => "鸿桥供应链",
            "HPTEX" => "海派通物流公司",
            "hq568" => "华强物流",
            "HXLWL" => "华夏龙物流",
            "HYLSD" => "好来运快递",
            "JGSD" => "京广速递",
            "JIUYE" => "九曳供应链",
            "JJKY" => "佳吉快运",
            "JLDT" => "嘉里物流",
            "JTKD" => "捷特快递",
            "JXD" => "急先达",
            "JYKD" => "晋越快递",
            "JYM" => "加运美",
            "JYWL" => "佳怡物流",
            "KYSY" => "跨越速运",
            "LB" => "龙邦快递",
            "LHT" => "联昊通速递",
            "MB" => "民邦快递",
            "MHKD" => "民航快递",
            "MLWL" => "明亮物流",
//            "NF" => "南方",
            "NEDA" => "能达速递",
            "PADTF" => "平安达腾飞快递",
            "PANEX" => "泛捷快递",
            "PJ" => "品骏",
            "PCA" => "PCA Express",
            "QCKD" => "全晨快递",
            "QFKD" => "全峰快递",
            "QRT" => "全日通快递",
            "QXT" => "全信通",
            "RFEX" => "瑞丰速递",
            "RFD" => "如风达",
            "SAD" => "赛澳递",
            "SAWL" => "圣安物流",
            "SBWL" => "盛邦物流",
            "SDWL" => "上大物流",
            "SF" => "顺丰快递",
            "SFWL" => "盛丰物流",
            "SHWL" => "盛辉物流",
            "ST" => "速通物流",
            "STWL" => "速腾快递",
            "SUBIDA" => "速必达物流",
            "SURE" => "速尔快递",
            "UAPEX" => "全一快递",
            "UEQ" => "UEQ Express",
            "UC" => "优速快递",
            "WJWL" => "万家物流",
            "WXWL" => "万象物流",
            "XBWL" => "新邦物流",
            "XFEX" => "信丰快递",
            "XYT" => "希优特",
            "XJ" => "新杰物流",
            "YADEX" => "源安达快递",
            "YCWL" => "远成物流",
            "YD" => "韵达快递",
            "YDH" => "义达国际物流",
            "YFEX" => "越丰物流",
            "YFHEX" => "原飞航物流",
            "YFSD" => "亚风快递",
            "YTKD" => "运通快递",
            "YTO" => "圆通速递",
            "YXKD" => "亿翔快递",
            "YUNDX" => "运东西",
            "YZPY" => "邮政平邮/小包",
            "ZENY" => "增益快递",
            "ZHQKD" => "汇强快递",
            "ZTE" => "众通快递",
            "ZTKY" => "中铁快运",
            "ZTO" => "中通速递",
            "ZTWL" => "中铁物流",
            "ZYWL" => "中邮物流",
            "AAE" => "AAE全球专递",
            "ACS" => "ACS雅仕快递",
            "ADP" => "ADP Express Tracking",
            "ANGUILAYOU" => "安圭拉邮政",
            "AOMENYZ" => "澳门邮政",
            "APAC" => "APAC",
            "ARAMEX" => "Aramex",
            "AT" => "奥地利邮政",
            "AUSTRALIA" => "Australia Post Tracking",
            "BEL" => "比利时邮政",
            "BHT" => "BHT快递",
            "BILUYOUZHE" => "秘鲁邮政",
            "BR" => "巴西邮政",
            "BUDANYOUZH" => "不丹邮政",
            "CA" => "加拿大邮政",
            "D4PX" => "递四方速递",
            "DHL" => "DHL",
            "DHL_EN" => "DHL(英文版)",
            "DHL_GLB" => "DHL全球",
            "DHLGM" => "DHL Global Mail",
            "DK" => "丹麦邮政",
            "DPD" => "DPD",
            "DPEX" => "DPEX",
            "EMSGJ" => "EMS国际",
            "ESHIPPER" => "EShipper",
            "GJEYB" => "国际e邮宝",
            "GJYZ" => "国际邮政包裹",
            "GLS" => "GLS",
            "IADLSQDYZ" => "安的列斯群岛邮政",
            "IADLYYZ" => "澳大利亚邮政",
            "IAEBNYYZ" => "阿尔巴尼亚邮政",
            "IAEJLYYZ" => "阿尔及利亚邮政",
            "IAFHYZ" => "阿富汗邮政",
            "IAGLYZ" => "安哥拉邮政",
            "IAGTYZ" => "阿根廷邮政",
            "IAJYZ" => "埃及邮政",
            "IALBYZ" => "阿鲁巴邮政",
            "IALQDYZ" => "奥兰群岛邮政",
            "IALYYZ" => "阿联酋邮政",
            "IAMYZ" => "阿曼邮政",
            "IASBJYZ" => "阿塞拜疆邮政",
            "IASEBYYZ" => "埃塞俄比亚邮政",
            "IASNYYZ" => "爱沙尼亚邮政",
            "IASSDYZ" => "阿森松岛邮政",
            "IBCWNYZ" => "博茨瓦纳邮政",
            "IBDLGYZ" => "波多黎各邮政",
            "IBDYZ" => "冰岛邮政",
            "IBELSYZ" => "白俄罗斯邮政",
            "IBHYZ" => "波黑邮政",
            "IBJLYYZ" => "保加利亚邮政",
            "IBJSTYZ" => "巴基斯坦邮政",
            "IBLNYZ" => "黎巴嫩邮政",
            "IBLSD" => "便利速递",
            "IBLWYYZ" => "玻利维亚邮政",
            "IBLYZ" => "巴林邮政",
            "IBMDYZ" => "百慕达邮政",
            "IBOLYZ" => "波兰邮政",
            "IBTD" => "宝通达",
            "IBYB" => "贝邮宝",
            "ICKY" => "出口易",
            "IDFWL" => "达方物流",
            "IDGYZ" => "德国邮政",
            "IE" => "爱尔兰邮政",
            "IEGDEYZ" => "厄瓜多尔邮政",
            "IELSYZ" => "俄罗斯邮政",
            "IELTLYYZ" => "厄立特里亚邮政",
            "IFTWL" => "飞特物流",
            "IGDLPDEMS" => "瓜德罗普岛EMS",
            "IGDLPDYZ" => "瓜德罗普岛邮政",
            "IGJESD" => "俄速递",
            "IGLBYYZ" => "哥伦比亚邮政",
            "IGLLYZ" => "格陵兰邮政",
            "IGSDLJYZ" => "哥斯达黎加邮政",
            "IHGYZ" => "韩国邮政",
            "IHHWL" => "华翰物流",
            "IHLY" => "互联易",
            "IHSKSTYZ" => "哈萨克斯坦邮政",
            "IHSYZ" => "黑山邮政",
            "IJBBWYZ" => "津巴布韦邮政",
            "IJEJSSTYZ" => "吉尔吉斯斯坦邮政",
            "IJKYZ" => "捷克邮政",
            "IJNYZ" => "加纳邮政",
            "IJPZYZ" => "柬埔寨邮政",
            "IKNDYYZ" => "克罗地亚邮政",
            "IKNYYZ" => "肯尼亚邮政",
            "IKTDWEMS" => "科特迪瓦EMS",
            "IKTDWYZ" => "科特迪瓦邮政",
            "IKTEYZ" => "卡塔尔邮政",
            "ILBYYZ" => "利比亚邮政",
            "ILKKD" => "林克快递",
            "ILMNYYZ" => "罗马尼亚邮政",
            "ILSBYZ" => "卢森堡邮政",
            "ILTWYYZ" => "拉脱维亚邮政",
            "ILTWYZ" => "立陶宛邮政",
            "ILZDSDYZ" => "列支敦士登邮政",
            "IMEDFYZ" => "马尔代夫邮政",
            "IMEDWYZ" => "摩尔多瓦邮政",
            "IMETYZ" => "马耳他邮政",
            "IMJLGEMS" => "孟加拉国EMS",
            "IMLGYZ" => "摩洛哥邮政",
            "IMLQSYZ" => "毛里求斯邮政",
            "IMLXYEMS" => "马来西亚EMS",
            "IMLXYYZ" => "马来西亚邮政",
            "IMQDYZ" => "马其顿邮政",
            "IMTNKEMS" => "马提尼克EMS",
            "IMTNKYZ" => "马提尼克邮政",
            "IMXGYZ" => "墨西哥邮政",
            "INFYZ" => "南非邮政",
            "INRLYYZ" => "尼日利亚邮政",
            "INWYZ" => "挪威邮政",
            "IPTYYZ" => "葡萄牙邮政",
            "IQQKD" => "全球快递",
            "IQTWL" => "全通物流",
            "ISDYZ" => "苏丹邮政",
            "ISEWDYZ" => "萨尔瓦多邮政",
            "ISEWYYZ" => "塞尔维亚邮政",
            "ISLFKYZ" => "斯洛伐克邮政",
            "ISLWNYYZ" => "斯洛文尼亚邮政",
            "ISNJEYZ" => "塞内加尔邮政",
            "ISPLSYZ" => "塞浦路斯邮政",
            "ISTALBYZ" => "沙特阿拉伯邮政",
            "ITEQYZ" => "土耳其邮政",
            "ITGYZ" => "泰国邮政",
            "ITLNDHDBGE" => "特立尼达和多巴哥EMS",
            "ITNSYZ" => "突尼斯邮政",
            "ITSNYYZ" => "坦桑尼亚邮政",
            "IWDMLYZ" => "危地马拉邮政",
            "IWGDYZ" => "乌干达邮政",
            "IWKLEMS" => "乌克兰EMS",
            "IWKLYZ" => "乌克兰邮政",
            "IWLGYZ" => "乌拉圭邮政",
            "IWLYZ" => "文莱邮政",
            "IWZBKSTEMS" => "乌兹别克斯坦EMS",
            "IWZBKSTYZ" => "乌兹别克斯坦邮政",
            "IXBYYZ" => "西班牙邮政",
            "IXFLWL" => "小飞龙物流",
            "IXGLDNYYZ" => "新喀里多尼亚邮政",
            "IXJPEMS" => "新加坡EMS",
            "IXJPYZ" => "新加坡邮政",
            "IXLYYZ" => "叙利亚邮政",
            "IXLYZ" => "希腊邮政",
            "IXPSJ" => "夏浦世纪",
            "IXPWL" => "夏浦物流",
            "IXXLYZ" => "新西兰邮政",
            "IXYLYZ" => "匈牙利邮政",
            "IYDLYZ" => "意大利邮政",
            "IYDNXYYZ" => "印度尼西亚邮政",
            "IYDYZ" => "印度邮政",
            "IYGYZ" => "英国邮政",
            "IYLYZ" => "伊朗邮政",
            "IYMNYYZ" => "亚美尼亚邮政",
            "IYMYZ" => "也门邮政",
            "IYNYZ" => "越南邮政",
            "IYSLYZ" => "以色列邮政",
            "IYTG" => "易通关",
            "IYWWL" => "燕文物流",
            "IZBLTYZ" => "直布罗陀邮政",
            "IZLYZ" => "智利邮政",
            "JP" => "日本邮政",
            "NL" => "荷兰邮政",
            "ONTRAC" => "ONTRAC",
            "QQYZ" => "全球邮政",
            "RDSE" => "瑞典邮政",
            "SWCH" => "瑞士邮政",
            "TAIWANYZ" => "台湾邮政",
            "TNT" => "TNT快递",
            "UPS" => "UPS",
            "USPS" => "USPS美国邮政",
            "YAMA" => "日本大和运输(Yamato)",
            "YODEL" => "YODEL",
            "YUEDANYOUZ" => "约旦邮政",
            "BN" => "笨鸟国际",
            "ZY_AG" => "爱购转运",
            "ZY_AOZ" => "爱欧洲",
            "ZY_AUSE" => "澳世速递",
            "ZY_AXO" => "AXO",
            "ZY_AZY" => "澳转运",
            "ZY_BDA" => "八达网",
            "ZY_BEE" => "蜜蜂速递",
            "ZY_BH" => "贝海速递",
            "ZY_BL" => "百利快递",
            "ZY_BM" => "斑马物流",
            "ZY_BOZ" => "败欧洲",
            "ZY_BT" => "百通物流",
            "ZY_BYECO" => "贝易购",
            "ZY_CM" => "策马转运",
            "ZY_CTM" => "赤兔马转运",
            "ZY_CUL" => "CUL中美速递",
            "ZY_DGHT" => "德国海淘之家",
            "ZY_DYW" => "德运网",
            "ZY_EFS" => "EFS POST",
            "ZY_ESONG" => "宜送转运",
            "ZY_ETD" => "ETD",
            "ZY_FD" => "飞碟快递",
            "ZY_FG" => "飞鸽快递",
            "ZY_FLSD" => "风雷速递",
            "ZY_FX" => "风行快递",
            "ZY_FXSD" => "风行速递",
            "ZY_FY" => "飞洋快递",
            "ZY_HC" => "皓晨快递",
            "ZY_HCYD" => "皓晨优递",
            "ZY_HDB" => "海带宝",
            "ZY_HFMZ" => "汇丰美中速递",
            "ZY_HJSD" => "豪杰速递",
            "ZY_HTAO" => "360hitao转运",
            "ZY_HTCUN" => "海淘村",
            "ZY_HTKE" => "365海淘客",
            "ZY_HTONG" => "华通快运",
            "ZY_HXKD" => "海星桥快递",
            "ZY_HXSY" => "华兴速运",
            "ZY_HYSD" => "海悦速递",
            "ZY_IHERB" => "LogisticsY",
            "ZY_JA" => "君安快递",
            "ZY_JD" => "时代转运",
            "ZY_JDKD" => "骏达快递",
            "ZY_JDZY" => "骏达转运",
            "ZY_JH" => "久禾快递",
            "ZY_JHT" => "金海淘",
            "ZY_LBZY" => "联邦转运FedRoad",
            "ZY_LPZ" => "领跑者快递",
            "ZY_LX" => "龙象快递",
            "ZY_LZWL" => "量子物流",
            "ZY_MBZY" => "明邦转运",
            "ZY_MGZY" => "美国转运",
            "ZY_MJ" => "美嘉快递",
            "ZY_MST" => "美速通",
            "ZY_MXZY" => "美西转运",
            "ZY_MZ" => "168 美中快递",
            "ZY_OEJ" => "欧e捷",
            "ZY_OZF" => "欧洲疯",
            "ZY_OZGO" => "欧洲GO",
            "ZY_QMT" => "全美通",
            "ZY_QQEX" => "QQ-EX",
            "ZY_RDGJ" => "润东国际快线",
            "ZY_RT" => "瑞天快递",
            "ZY_RTSD" => "瑞天速递",
            "ZY_SCS" => "SCS国际物流",
            "ZY_SDKD" => "速达快递",
            "ZY_SFZY" => "四方转运",
            "ZY_SOHO" => "SOHO苏豪国际",
            "ZY_SONIC" => "Sonic-Ex速递",
            "ZY_ST" => "上腾快递",
            "ZY_TCM" => "通诚美中快递",
            "ZY_TJ" => "天际快递",
            "ZY_TM" => "天马转运",
            "ZY_TN" => "滕牛快递",
            "ZY_TPAK" => "TrakPak",
            "ZY_TPY" => "太平洋快递",
            "ZY_TSZ" => "唐三藏转运",
            "ZY_TTHT" => "天天海淘",
            "ZY_TWC" => "TWC转运世界",
            "ZY_TX" => "同心快递",
            "ZY_TY" => "天翼快递",
            "ZY_TZH" => "同舟快递",
            "ZY_UCS" => "UCS合众快递",
            "ZY_WDCS" => "文达国际DCS",
            "ZY_XC" => "星辰快递",
            "ZY_XDKD" => "迅达快递",
            "ZY_XDSY" => "信达速运",
            "ZY_XF" => "先锋快递",
            "ZY_XGX" => "新干线快递",
            "ZY_XIYJ" => "西邮寄",
            "ZY_XJ" => "信捷转运",
            "ZY_YGKD" => "优购快递",
            "ZY_YJSD" => "友家速递(UCS)",
            "ZY_YPW" => "云畔网",
            "ZY_YQ" => "云骑快递",
            "ZY_YQWL" => "一柒物流",
            "ZY_YSSD" => "优晟速递",
            "ZY_YSW" => "易送网",
            "ZY_YTUSA" => "运淘美国",
            "ZY_ZCSD" => "至诚速递",
        );
                
        return $all_code;
}

//kd100快递公司对应kdn的编码
function kd100_2_kdn(){
    $code = array(
        'shunfeng'  =>  'SF',
        'ems'   =>  'EMS',
        'zhaijisong'    =>  'ZJS',
        'yuantong'  =>  'YTO',
//        'HTKY'  =>  'HTKY',//百世快递
        'zhongtong' =>  'ZTO',
        'yunda' =>  'YD',
        'shentong'  =>  'STO',
        'debangwuliu'   =>  'DBL',
        'youshuwuliu'   =>  'UC',
//        'JD'    =>  'JD',//京东
        'xinfengwuliu'  =>  'XFEX',
        'quanfengkuaidi'    =>  'QFKD',
//        'KYSY'  =>  'KYSY',//跨越速运
//        'ANE'  =>   'ANE',//安能小包
        'kuaijiesudi'   =>  'FAST',
        'guotongkuaidi' =>  'GTO',
        'tiantian'  =>  'HHTT',
        'quanyikuaidi'  =>  'UAPEX',
        'zhongtiekuaiyun'   =>  'ZTKY',
    );
    
    return $code;
}


//快递鸟可以直接使用电子面单的快递公司编号
function kdn_can_demo(){
    $code = array(
        'SF','EMS','FAST','ZJS','ZTKY','UAPEX','YD','HTKY','YTO'
    );
    return $code;
}

//快递鸟可用的电子面单
function kdn_use_demo(){
    $code = array(
        'ZTO'
    );
    return $code;
}


/**
 * 错误提示页面
 * @param type $content         提示内容
 * @param type $url             直接跳转页面
 * @param type $return_url      提示页面左上返回按钮链接，默认为回退
 */
function error_tip($content, $url,$return_url='') {
    if (!$url) {
        $url = __APP__.'/admin/tip/error';
    }
    $url = $url . '?msg='. $content.'&return_url='.$return_url;
    header("location:$url");
    exit();
}



/* 
    * 经典的概率算法， 
    * $proArr是一个预先设置的数组， 
    * 假设数组为：array(100,200,300，400)， 
    * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，  
    * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间， 
    * 在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。 
    * 这样 筛选到最终，总会有一个数满足要求。 
    * 就相当于去一个箱子里摸东西， 
    * 第一个不是，第二个不是，第三个还不是，那最后一个一定是。 
    * 这个算法简单，而且效率非常 高， 
    */  
   function get_rand($proArr) {   
       $result = '0';    
       //概率数组的总概率精度   
       $proSum = array_sum($proArr);    
       //概率数组循环   
       foreach ($proArr as $key => $proCur) {   
           $randNum = mt_rand(1, $proSum);   
           if ($randNum <= $proCur) {   
               $result = $key;   
               break;   
           } else {   
               $proSum -= $proCur;   
           }         
       }   
       unset ($proArr);    
       return $result;   
   }
   
   /**
 * 获得当前月/上个月份
 * @param int $type 0当前月1上个月2下个月
 * @return string
 */
function get_month($type = 0) {
    $tmp_date=date('Ym');
    if (!$type) {
        return $tmp_date;
    } else {
        //切割出年份
        $tmp_year=substr($tmp_date,0,4);
        //切割出月份
        $tmp_mon =substr($tmp_date,4,2);
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
        $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
        if($type == 2){
            //得到当前月的下一个月   
            return $fm_next_month=date("Ym",$tmp_nextmonth);          
        }else if($type == 1){
            //得到当前月的上一个月   
            return $fm_forward_month=date("Ym",$tmp_forwardmonth);           
        }
    }
} 



//----------start 双向加密（该版本用于链接上，公开性较高，尽量不要用于保密性高的地方）----------------
function  tiriEncode($str , $factor = 0){
    $len = strlen($str);
    if(!$len){
        return;
    }
    if($factor  === 0){
        $factor = mt_rand(1, min(255 , ceil($len / 3)));
    }
    $c = $factor % 8;

    $slice = str_split($str ,$factor);
    for($i=0;$i < count($slice);$i++){
        for($j=0;$j< strlen($slice[$i]) ;$j ++){
            $slice[$i][$j] = chr(ord($slice[$i][$j]) + $c + $i);
        }
    }
    $ret = pack('C' , $factor).implode('' , $slice);
    return base64URLEncode($ret);
}

function tiriDecode($str){  
    if($str == ''){
        return;
    }     
    $str = base64URLDecode($str);
    $factor =  ord(substr($str , 0 ,1));
    $c = $factor % 8;
    $entity = substr($str , 1);
    $slice = str_split($entity , $factor);
    if(!$slice){
        return false;
    }
    for($i=0;$i < count($slice); $i++){
        for($j =0 ; $j < strlen($slice[$i]); $j++){
            $slice[$i][$j] = chr(ord($slice[$i][$j]) - $c - $i );
        }
    }
    return implode($slice);
}

function base64URLEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64URLDecode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function stringXor($str){
    for ($i = 0; $i < strlen($str); ++$i) {
        $str[$i] = chr(ord($str[$i]) ^ 0x7F);
    }
    return $str;
}
//----------end 双向加密----------------



//得到某个数字的范围
/**
 * PS:
 * $stage_data = [
 *  100000,
 *  200000,
 *  300000,
 *  400000,
 *  600000,
 *  800000
 *  ];
 * 
 *  $stage_num = 390000;
 * 
 *   $res = binarySearch($rebate_percent,$stage_num);
 * 
 *   echo $res;
 * 
 *  (result: 2)
 */
function binarySearch(&$stage_data,$stage_num){
    $count = count($stage_data);
    
    array_push($stage_data,$stage_num);
    $data = array_unique($stage_data);
    asort($data);
    $data = array_values($data);
    
    $key = array_search($stage_num,$data);
    
    $new_count = count($data);
    
    if( $key != 0 && $new_count != $count ){
        $key--;
    }
    
    return $key;
}

//读取缓存团队
function get_team_path_by_cache() {
//    if (!F('team_path')) {
//        $default_team = C('DEFAULT_TEAM');
//        $where['is_lowest'] = 1;
//        $users = M('distributor')->where($where)->field("id,$default_team")->select();
//        F('team_path', $users);
//    } else {
//        $users = F('team_path');
//    }
    $default_team = C('DEFAULT_TEAM');
    $where['is_lowest'] = 1;
    $users = M('distributor')->where($where)->field("id,$default_team")->select();
    return $users;
}

//清除团队缓存
function clean_team_path_cache() {
    F('team_path', NULL);
}

//自动给字符串标星号
function asterisk($str){
    
    if( is_array($str) ){
        return $str;
    }
    $asterisk = '***';
    $str_len = mb_strlen($str);
    
    if( $str_len < 4 ){
        return $asterisk;
    }
    
    $num = $str_len/4;
    
    if( $num > 2 ){
        $num = 3;
    }
    $end_beg = $str_len-$num;
    
    $new_str = displaystr($str, 0,$num).$asterisk.displaystr($str, $end_beg,$num);
    
    return $new_str;
}

//中英文混合都可无乱码截取的方法
function displaystr($str, $start, $lenth){  
        $len = strlen($str);  
        $r = array();  
        $n = 0;  
        $m = 0;  
        for($i = 0; $i < $len; $i++) {  
            $x = substr($str, $i, 1);  
            $a  = base_convert(ord($x), 10, 2);  
            $a = substr('00000000'.$a, -8);  
            if ($n < $start){  
                if (substr($a, 0, 1) == 0) {  
                }elseif (substr($a, 0, 3) == 110) {  
                    $i += 1;  
                }elseif (substr($a, 0, 4) == 1110) {  
                    $i += 2;  
                }  
                $n++;  
            }else{  
                if (substr($a, 0, 1) == 0) {  
                    $r[ ] = substr($str, $i, 1);  
                }elseif (substr($a, 0, 3) == 110) {  
                    $r[ ] = substr($str, $i, 2);  
                    $i += 1;  
                }elseif (substr($a, 0, 4) == 1110) {  
                    $r[ ] = substr($str, $i, 3);  
                    $i += 2;  
                }else{  
                    $r[ ] = '';  
                }  
                if (++$m >= $lenth){  
                    break;  
                }  
            }  
        }  
        return join('',$r);  
    }   



/**
 * Create By TuJia 
 */
//获取地区信息
function get_area($myip = null){
    import("ORG.Net.IpLocation");
    $Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
    $data = $Ip->getlocation($myip); // 获取某个IP地址所在的位置
    return $data['area'];
}



/**
 * 默认值
 * @param  [type] $default [description]
 * @param  [type] $val     [description]
 * @return [type]          [description]
 */
function default_val($default, $val){
    if(empty($val)) return $default;
    return $val;
}



    /**
     * 内容链接
     * @param  [type] $link_str [description]
     * @return [type]           [description]
     */
    function content_links($link_str){
        $link_arr   = explode("\n",$link_str);
        $result     = array();
        foreach($link_arr as $key=>$value){
            $arr    = explode('|',$value);
            $result[$key]['name'] = $arr[0];
            $result[$key]['link'] = trim($arr[1]);
        }
        return $result;
    }

    //linux系统探测
    function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo"))) return false;
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
        if (false !== is_array($model[1]))
        {
            $res['cpu']['num'] = sizeof($model[1]);
            /*
            for($i = 0; $i < $res['cpu']['num']; $i++)
            {
                $res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
                $res['cpu']['mhz'][] = $mhz[1][$i];
                $res['cpu']['cache'][] = $cache[1][$i];
                $res['cpu']['bogomips'][] = $bogomips[1][$i];
            }*/
            if($res['cpu']['num']==1)
                $x1 = '';
            else
                $x1 = ' ×'.$res['cpu']['num'];
            $mhz[1][0] = ' | 频率:'.$mhz[1][0];
            $cache[1][0] = ' | 二级缓存:'.$cache[1][0];
            $bogomips[1][0] = ' | Bogomips:'.$bogomips[1][0];
            $res['cpu']['model'][] = $model[1][0].$mhz[1][0].$cache[1][0].$bogomips[1][0].$x1;
            if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
            if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
            if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
            if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
        }

        // NETWORK

        // UPTIME
        if (false === ($str = @file("/proc/uptime"))) return false;
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";

        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) return false;
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

        $res['memTotal'] = round($buf[1][0]/1024, 2);
        $res['memFree'] = round($buf[2][0]/1024, 2);
        $res['memBuffers'] = round($buffers[1][0]/1024, 2);
        $res['memCached'] = round($buf[3][0]/1024, 2);
        $res['memUsed'] = $res['memTotal']-$res['memFree'];
        $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;

        $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
        $res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
        $res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0; //真实内存使用率

        $res['memCachedPercent'] = (floatval($res['memCached'])!=0)?round($res['memCached']/$res['memTotal']*100,2):0; //Cached内存使用率

        $res['swapTotal'] = round($buf[4][0]/1024, 2);
        $res['swapFree'] = round($buf[5][0]/1024, 2);
        $res['swapUsed'] = round($res['swapTotal']-$res['swapFree'], 2);
        $res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;

        // LOAD AVG
        if (false === ($str = @file("/proc/loadavg"))) return false;
        $str = explode(" ", implode("", $str));
        $str = array_chunk($str, 4);
        $res['loadAvg'] = implode(" ", $str[0]);

        return $res;
    }

    //FreeBSD系统探测
    function sys_freebsd()
    {
        //CPU
        if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
        $res['cpu']['model'] = get_key("hw.model");
        //LOAD AVG
        if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) return false;
        //UPTIME
        if (false === ($buf = get_key("kern.boottime"))) return false;
        $buf = explode(' ', $buf);
        $sys_ticks = time() - intval($buf[3]);
        $min = $sys_ticks / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";
        //MEMORY
        if (false === ($buf = get_key("hw.physmem"))) return false;
        $res['memTotal'] = round($buf/1024/1024, 2);

        $str = get_key("vm.vmtotal");
        preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
        preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);

        $res['memRealUsed'] = round($buf[0][2]/1024, 2);
        $res['memCached'] = round($buff[0][2]/1024, 2);
        $res['memUsed'] = round($buf[0][1]/1024, 2) + $res['memCached'];
        $res['memFree'] = $res['memTotal'] - $res['memUsed'];
        $res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;

        $res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;

        return $res;
    }

    //取得参数值 FreeBSD
    function get_key($keyName)
    {
        return do_command('sysctl', "-n $keyName");
    }

    //确定执行文件位置 FreeBSD
    function find_command($commandName)
    {
        $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
        foreach($path as $p) 
        {
            if (@is_executable("$p/$commandName")) return "$p/$commandName";
        }
        return false;
    }

    //执行系统命令 FreeBSD
    function do_command($commandName, $args)
    {
        $buffer = "";
        if (false === ($command = find_command($commandName))) return false;
        if ($fp = @popen("$command $args", 'r')) 
        {
            while (!@feof($fp))
            {
                $buffer .= @fgets($fp, 4096);
            }
            return trim($buffer);
        }
        return false;
    }

    //windows系统探测
    function sys_windows()
    {
        if (PHP_VERSION >= 5)
        {
            $objLocator = new COM("WbemScripting.SWbemLocator");
            $wmi = $objLocator->ConnectServer();
            $prop = $wmi->get("Win32_PnPEntity");
        }
        else
        {
            return false;
        }

        //CPU
        $cpuinfo = GetWMI($wmi,"Win32_Processor", array("Name","L2CacheSize","NumberOfCores"));
        $res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
        if (null == $res['cpu']['num']) 
        {
            $res['cpu']['num'] = 1;
        }/*
        for ($i=0;$i<$res['cpu']['num'];$i++)
        {
            $res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";
            $res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";
        }*/
        $cpuinfo[0]['L2CacheSize'] = ' ('.$cpuinfo[0]['L2CacheSize'].')';
        if($res['cpu']['num']==1)
            $x1 = '';
        else
            $x1 = ' ×'.$res['cpu']['num'];
        $res['cpu']['model'] = $cpuinfo[0]['Name'].$cpuinfo[0]['L2CacheSize'].$x1;
        // SYSINFO
        $sysinfo = GetWMI($wmi,"Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
        $sysinfo[0]['Caption']=iconv('GBK', 'UTF-8',$sysinfo[0]['Caption']);
        $sysinfo[0]['CSDVersion']=iconv('GBK', 'UTF-8',$sysinfo[0]['CSDVersion']);
        $res['win_n'] = $sysinfo[0]['Caption']." ".$sysinfo[0]['CSDVersion']." 序列号:{$sysinfo[0]['SerialNumber']} 于".date('Y年m月d日H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))."安装";
        //UPTIME
        $res['uptime'] = $sysinfo[0]['LastBootUpTime'];

        $sys_ticks = 3600*8 + time() - strtotime(substr($res['uptime'],0,14));
        $min = $sys_ticks / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) $res['uptime'] = $days."天";
        if ($hours !== 0) $res['uptime'] .= $hours."小时";
        $res['uptime'] .= $min."分钟";

        //MEMORY
        $res['memTotal'] = round($sysinfo[0]['TotalVisibleMemorySize']/1024,2);
        $res['memFree'] = round($sysinfo[0]['FreePhysicalMemory']/1024,2);
        $res['memUsed'] = $res['memTotal']-$res['memFree']; //上面两行已经除以1024,这行不用再除了
        $res['memPercent'] = round($res['memUsed'] / $res['memTotal']*100,2);

        $swapinfo = GetWMI($wmi,"Win32_PageFileUsage", array('AllocatedBaseSize','CurrentUsage'));

        // LoadPercentage
        $loadinfo = GetWMI($wmi,"Win32_Processor", array("LoadPercentage"));
        $res['loadAvg'] = $loadinfo[0]['LoadPercentage'];

        return $res;
    }

    function GetWMI($wmi,$strClass, $strValue = array())
    {
        $arrData = array();

        $objWEBM = $wmi->Get($strClass);
        $arrProp = $objWEBM->Properties_;
        $arrWEBMCol = $objWEBM->Instances_();
        foreach($arrWEBMCol as $objItem) 
        {
            @reset($arrProp);
            $arrInstance = array();
            foreach($arrProp as $propItem) 
            {
                eval("\$value = \$objItem->" . $propItem->Name . ";");
                if (empty($strValue)) 
                {
                    $arrInstance[$propItem->Name] = trim($value);
                } 
                else
                {
                    if (in_array($propItem->Name, $strValue)) 
                    {
                        $arrInstance[$propItem->Name] = trim($value);
                    }
                }
            }
            $arrData[] = $arrInstance;
        }
        return $arrData;
    }
    function isfun($funName = '')
    {
        if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
        return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
    }
    function isfun1($funName = '')
    {
        if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
        return (false !== function_exists($funName)) ? '√' : '×';
}
    //检测PHP设置参数
    function show($varName)
    {
        switch($result = get_cfg_var($varName))
        {
            case 0:
                return '<font color="red">×</font>';
            break;
            
            case 1:
                return '<font color="green">√</font>';
            break;
            
            default:
                return $result;
            break;
        }
    }