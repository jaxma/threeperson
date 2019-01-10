<?php

/**
 * 	topos--快递鸟
 */
class KdniaoAction extends Action {

    //电商ID
    private $EBusinessID;
    //电商加密私钥，快递鸟提供，注意保管，不要泄漏
    private $AppKey;
    //IP服务地址
    private $IP_SERVICE_URL = 'http://www.kdniao.com/External/GetIp.aspx';
    //请求url，正式环境地址：http://api.kdniao.cc/api/Eorderservice    测试环境地址：http://testapi.kdniao.cc:8081/api/EOrderService
    private $orderdemo_requrl = 'http://testapi.kdniao.cc:8081/api/Eorderservice';
    //批量打印接口
    private $orderdemo_printurl = 'http://www.kdniao.com/External/PrintOrder.aspx';
    
    //中通的账号密码
//    private $zto_id = '';
//    private $zto_pass = '';

    public function __construct() {
        $kdnapi = C('kdnapi');
        $IS_TEST = C('IS_TEST');
        
        if( !$IS_TEST ){
            $this->orderdemo_requrl = 'http://api.kdniao.cc/api/Eorderservice';
        }

        $this->EBusinessID = $kdnapi['EBusinessID'];
        $this->AppKey = $kdnapi['AppKey'];
    }

    //
    public function index() {
        
    }
    
    
    //获取电子面单
    public function print_orderdemo(){
	
        header("Content-type: text/html; charset=utf-8");

        $result = $this->get_orderdemo();
        
//        print_r($result);return;
        
        $Reason = isset($result['Reason'])?$result['Reason']:'接口返回错误！';
        $PrintTemplate = isset($result['PrintTemplate'])?$result['PrintTemplate']:NULL;
        $LogisticCode = isset($result['Order']['LogisticCode'])?$result['Order']['LogisticCode']:NULL;
        
        $returnjson = I('returnjson');
        $returnallinfo = isset($returnjson)?$returnjson:0;
        
        
        $this->ajaxReturn($result);return;
        
        if( $returnallinfo ){
            $this->ajaxReturn($result);
        }
        elseif( empty($PrintTemplate) ){
            //print_r($result);
            echo '<html><h1>'.$Reason.'</h1></html>';
            return;
        }
        
//        echo '<script>window.print();</script>';
        echo $PrintTemplate;
    }//end func print_orderdemo
    
    
    
    
    

    /**
     * 组装POST表单用于调用快递鸟批量打印接口页面
     */
    public function build_form() {
        //OrderCode:需要打印的订单号，和调用快递鸟电子面单的订单号一致，PortName：本地打印机名称，请参考使用手册设置打印机名称。支持多打印机同时打印。
//        $request_data = '[{"OrderCode":"234351215333113311353","PortName":"打印机名称一"},{"OrderCode":"234351215333113311354","PortName":"打印机名称二"}]';
        
        $OrderCode = I('OrderCode');
        $PortName = I('PortName');
        
        if( empty($PortName) && 0 ){
            $PortName = 'Microsoft Print to PDF';
        }
        
        if( !is_array($OrderCode) ){
            //print_r($OrderCode);
            echo '参数错误';
            return;
        }
        
        foreach( $OrderCode as $k=>$v ){
            $request_data[$k]['OrderCode'] = $v;
            $request_data[$k]['PortName'] = $PortName;
        }
        
//        $request_data = [
//            [
//                'OrderCode' => '1234561',
//                'PortName' => '打印机名称一',
//            ],
//            [
//                'OrderCode' => '12345611',
//                'PortName' => '打印机名称二',
//            ],
//        ];
        
        $request_data = json_encode($request_data,1);
        //echo $request_data;return;

        //$ip = get_ip();
        $ip = $this->kdn_get_ip();
        //echo $ip;return;

        $request_data_encode = urlencode($request_data);
        $data_sign = $this->encrypt($ip . $request_data_encode, $this->AppKey);
        
        //echo $ip . $request_data_encode. $this->AppKey;return;
        
        //是否预览，0-不预览 1-预览
        $is_priview = '0';
        
        //组装表单
        $form = '<form id="form1" method="POST" action="' . $this->orderdemo_printurl . '"><input type="text" name="RequestData" value="' . $request_data . '"/><input type="text" name="EBusinessID" value="' . $this->EBusinessID . '"/><input type="text" name="DataSign" value="' . $data_sign . '"/><input type="text" name="IsPriview" value="' . $is_priview . '"/></form>';
        $form = $form.'<script>form1.submit();</script>';
        
        //echo json_encode($form);return;
        
        //setLog('data:'.print_r(json_decode($request_data),1).'form:'.print_r($form,1).',encrypt:'.$ip .$request_data.$this->AppKey,'testform');
        
        print_r($form);
    }
    
    
    
    
    //---------------start 打印电子面单的相关逻辑---------------------------
    
    //获取电子面单
    private function get_orderdemo(){
        header("Content-type: text/html; charset=utf-8");
        
        $shipper_type = 'kd100';I('shippertype');
        
        $eorder["ShipperCode"] = I('ShipperCode');//快递公司
        $eorder["OrderCode"] = I('OrderCode');//订单编号
        $eorder['LogisticCode'] = I('LogisticCode');//快递单号
        $eorder["PayType"] = I('PayType');//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
        $eorder['IsReturnPrintTemplate'] = I('IsReturnPrintTemplate');//返回电子面单模板：0-不需要；1-需要
//        $eorder['Weight'] = I('Weight');//物品总重量kg
//        $eorder['Quantity'] = I('Quantity');//件数/包裹数
        $eorder['Remark'] = I('Remark');//备注
        //$eorder['Cost'] = I('Cost');//寄件费（运费）
        $eorder['MonthCode'] = I('MonthCode');//月结编码
        
        
//        print_r(I());return;
        
        if( !in_array($eorder["PayType"], array(1,2,3,4)) ){
            $eorder["PayType"] = 3;
        }
        
        if( empty($eorder['IsReturnPrintTemplate']) ){
            $eorder['IsReturnPrintTemplate'] = 1;
        }
        
        if( $shipper_type == 'kd100' ){
            $kd100_2_kdn_code = kd100_2_kdn();
            $ShipperCode = $eorder["ShipperCode"];
            if( !isset($kd100_2_kdn_code[$ShipperCode]) ){
                $result['Reason'] = '该物流公司未有电子面单！';
                return $result;
            }
            $eorder["ShipperCode"] = $kd100_2_kdn_code[$ShipperCode];
        }
        
        $kdn_can_demo_code = kdn_can_demo();
        $kdn_use_demo = kdn_use_demo();
        
        if( in_array($eorder["ShipperCode"],$kdn_use_demo) ){
            
            if( $eorder["ShipperCode"] == 'ZTO' ){
                $eorder['CustomerName'] = $this->zto_id;
                $eorder['CustomerPwd'] = $this->zto_pass;
            }
            else{
                $eorder['CustomerName'] = I('CustomerName');//电子面单客户账号（与快递网点申请）
                $eorder['CustomerPwd'] = I('CustomerPwd');//电子面单密码
            }
        }
        elseif( !in_array($eorder["ShipperCode"],$kdn_can_demo_code) ){
            $result['Reason'] = '该快递公司不在可用电子面单的列表中！当前可用的快递公司为'.  implode(',', $kdn_can_demo_code);
            return $result;
        }
        
        
        
        //寄件人信息
        $sender["Name"] = I('sendername');
        $sender["Mobile"] = I('sendermobile');
        $sender["ProvinceName"] = I('senderprovincename');
        $sender["CityName"] = I('sendercityname');
        $sender["ExpAreaName"] = I('senderexpareaName');
        $sender["Address"] = I('senderaddress');
        
        
        //收件人信息
        $receiver["Name"] = I('receivername');
        $receiver["Mobile"] = I('receivermobile');
        $receiver["ProvinceName"] = I('receiverprovincename');
        $receiver["CityName"] = I('receivercityname');
        $receiver["ExpAreaName"] = I('receiverexpareaName');
        $receiver["Address"] = I('receiveraddress');
        
        
        //自定义信息
        $return_number = I('return_number');//返回快递单号的url
        
        
//        print_r($receiver);
        
        //商品信息
        $commodityOne["GoodsName"] = I('GoodsName');//商品名称
        $commodityOne['Goodsquantity'] = I('Goodsquantity');//商品数量
        $commodityOne['GoodsPrice'] = I('GoodsPrice');//商品价格
        $commodityOne['GoodsDesc'] = I('GoodsDesc');//商品描述
        $commodityOne['GoodsWeight'] = I('GoodsWeight');//商品重量kg
        
        $result = $this->orderdemo($sender,$receiver,$commodityOne,$eorder);
        
        $result = json_decode($result,true);
        
//        if( !empty($return_number) && !empty($result['Order']['LogisticCode']) ){
//            $return_post = array(
//                'order_num' =>  $eorder['LogisticCode'],
//                'ordernumber'   =>  $result['Order']['LogisticCode'],
//            );
//            
//            $return_number_url = $return_number.'/radmin/receive/ordernb';
//            https_request($return_number,$return_post);
//        }
        
        return $result;
    }//end func get_orderdemo
    
    
    
    /**
     * 快递鸟电子面单接口
     * @param array $sender     //寄件人信息
     * @param array $receiver   //接单人信息
     * @param array $eorder     //订单基本信息
     * @return array
     */
    private function orderdemo($sender,$receiver,$commodityOne,$eorder=array()){
        
        if( empty($sender) || empty($receiver) ){
            $result['Reason'] = '提交的信息有误！';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        
        if( empty($eorder["ShipperCode"]) || empty($eorder["OrderCode"]) || empty($commodityOne["GoodsName"]) ){
            //print_r($eorder);
            $result['Reason'] = '快递公司、订单编号，以及商品名称必须填写完整！';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        
        
        //构造电子面单提交信息
        $eorder["ShipperCode"] = isset($eorder['ShipperCode'])?$eorder['ShipperCode']:'';
        $eorder["OrderCode"] = isset($eorder['OrderCode'])?$eorder['OrderCode']:'';//订单编号
        $eorder['LogisticCode'] = isset($eorder['LogisticCode'])?$eorder['LogisticCode']:'';//快递单号
        $eorder["PayType"] = isset($eorder["PayType"])?$eorder["PayType"]:3;//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
        $eorder["ExpType"] = 1;//快递类型：1-标准快件
        $eorder["IsReturnPrintTemplate"] = isset($eorder["IsReturnPrintTemplate"])?$eorder["IsReturnPrintTemplate"]:1;//是否返回电子面单模板
        
        
        //商品信息
        $commodityOne["GoodsName"] = isset($commodityOne["GoodsName"])?$commodityOne["GoodsName"]:'';;//商品名称
//        $commodityOne['Goodsquantity'] = 1;//商品数量
        $commodity[] = $commodityOne;
        
        
        
        $eorder["Sender"] = $sender;
        $eorder["Receiver"] = $receiver;
        $eorder["Commodity"] = $commodity;
        $eorder['MonthCode'] = $this->AppKey;
        $eorder['IsNotice'] = 1;//是否通知快递员上门揽件：0-通知；1-不通知；不填则默认为0
//        echo '断点0：';
//        print_r($eorder);
//        echo '<br />';
        
        $data = $this->orderTracesSubByJson($eorder,'1007',$this->orderdemo_requrl);
        
        $data = json_encode(json_decode( $data,true));
        
        return $data;
    }//end func orderdemo
    
    
    //---------------end 打印电子面单的相关逻辑---------------------------
    
    
    
    //---------------start 批量打印电子面单的相关逻辑---------------------------
    
    /**
     * 判断是否为内网IP
     * @param ip IP
     * @return 是否内网IP
     */
    private function is_private_ip($ip) {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * 获取客户端IP(非用户服务器IP)
     * @return 客户端IP
     */
    private function kdn_get_ip() {
        //获取客户端IP
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!$ip || $this->is_private_ip($ip)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->IP_SERVICE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            return $output;
        } else {
            return 'error';
        }
    }
    
    //---------------end 批量打印电子面单的相关逻辑---------------------------
    

    //=========================接口获取的相关方法=====================================

    /**
    * Json方式  物流信息订阅
    */
    function orderTracesSubByJson($requestData,$RequestType,$ReqURL) {

//        echo '断点1：';
//        print_r($requestData);
//        echo '<br />';
        
        $requestData = json_encode($requestData,JSON_UNESCAPED_UNICODE);

//        echo '断点2：';
//        print_r(json_decode($requestData,TRUE));return;
        
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => $RequestType,
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
        $result = $this->sendPost($ReqURL, $datas);
        
        //根据公司业务处理返回的信息......
        
        
        return $result;
    }//end func orderTracesSubByJson

    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port'] = 80;
        }
        
        header("Content-type: text/html; charset=utf-8");
        
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
                if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                        break;
                }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd); 

        return $gets;
    }//end func sendPost

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data . $appkey)));
    }//end func encrypt

    
    //=========================end 接口获取的相关方法=====================================

    //=========================物流接口返回规定格式=====================================
    /**
     * 输出正确信息
     *
     * @param string $info
     * @return array
     */
    private function success_info($info = '') {

        $return_data = array(
            'EBusinessID' => $this->EBusinessID,
            'UpdateTime' => date('Y-m-d H:i:s'),
            'Success' => 'true',
            'Reason' => $info,
        );

        //$return_data = utf8_encode($return_data);
        return $return_data;
    }


    /**
     * 输出错误信息
     *
     * @param string $error_code
     * @return array
     */
    private function error_info($error_code) {

        $error_info = $this->error_code_info($error_code);
        $return_data = array(
            'EBusinessID' => $this->EBusinessID,
            'UpdateTime' => date('Y-m-d H:i:s'),
            'Success' => 'false',
            'Reason' => $error_info,
        );

        //$return_data = utf8_encode($return_data);
        return $return_data;
    }


    /**
     * 错误信息
     * @param string $error_code
     * @return string
     */
    private function error_code_info($error_code) {

        $error_info = array(
            '10000' => array(
                'Data transfer error', //数据传输错误
            ),
//            '10001' => array(
//                'Data transfer error',//数据传输错误
//            ),
            '10002' => array(
                'Authentication failed', //认证失败
            ),
            '20001' => array(
                'RequestType lose'
            ),
            '20002' => array(
                'Save error'
            ),
            '99999' => array(
                'system error', //系统错误
            ),
        );

        if (!isset($error_info[$error_code])) {
            $error_code = '99999';
        }

        $return_info = $error_info[$error_code][0];

        return $return_info;
    }

    //=========================end 物流接口调用=====================================
}

?>