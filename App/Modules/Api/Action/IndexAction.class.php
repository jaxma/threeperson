<?php

/**
 * 	topos
 */
class IndexAction extends Action {

    //
    public function index() {
        
    }
    
    //测试打印表单
    public function test_kdn(){
        header("Content-type: text/html; charset=utf-8");
        $url = __APP__.'/api/kdniao/print_orderdemo';
        $url = 'localhost/topos/api/kdniao/print_orderdemo';
        
      $data = [
          'ShipperCode'   =>  'SF',//快递公司编码
          'OrderCode'     => '123456789',//订单号（不可重复提交，重复提交系统会返回具体错误代码。）
          'LogisticCode'  =>  '789456123',//快递单号
          'PayType'       =>  '1',//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
          'Cost'          =>  '99.99',
          'sendername'    =>  '测试寄送人',
          'sendermobile'  =>  '13678123456',
          'senderprovincename'    =>  '广东省',//不要缺少“省”
          'sendercityname'    =>  '广州市',//不要缺少“市”
          'senderexpareaName' =>  '天河区',//不要缺少“区”或“县”
          'senderaddress' =>  '测试地址',
          'receivername'  =>  '测试收货人',
          'receivermobile' =>  '13678123456',
          'receiverprovincename'  =>  '广东省',
          'receivercityname'  =>  '广州市',
          'receiverexpareaName'   =>  '萝岗区',
          'receiveraddress'   =>  '测试地址',
//        'return_number' =>  '1234561',
          'Remark'        =>  '测试的电子面单',//备注
          'GoodsName' =>  '测试商品',//商品名称
          'Goodsquantity' =>  '100',//商品数量
          'GoodsPrice'    =>  '88.88',//商品价格
          'GoodsDesc'     =>  '用于测试',//商品描述
          'IsReturnPrintTemplate' =>  '0',//返回电子面单模板：0-不需要；1-需要（如果调用批量打印则封装这个为0）
          'returnjson' =>  1,//是否返回json格式
      ];
        
//        $data = [
//            'ShipperCode'   =>  'SF',//快递公司编码
////          'ShipperCode'   =>  I('ShipperCode'),//快递公司编码
//            'OrderCode'     => I('OrderCode'),//订单号（不可重复提交，重复提交系统会返回具体错误代码。）
//            'LogisticCode'  =>  I('LogisticCode'),//快递单号
//            'PayType'       =>  '1',//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付
//            'Cost'          =>  I('Cost'),
//            'sendername'    =>  I('sendername'),
//            'sendermobile'  =>  I('sendermobile'),
//            'senderprovincename'    =>  I('senderprovincename'),//不要缺少“省”
//            'sendercityname'    =>  I('sendercityname'),//不要缺少“市”
//            'senderexpareaName' =>  I('senderexpareaName'),//不要缺少“区”或“县”
//            'senderaddress' =>  I('senderaddress'),
//            'receivername'  =>  I('receivername'),
//            'receivermobile' =>  I('receivermobile'),
//            'receiverprovincename'  =>  I('receiverprovincename'),
//            'receivercityname'  =>  I('receivercityname'),
//            'receiverexpareaName'   =>  I('receiverexpareaName'),
//            'receiveraddress'   =>  I('receiveraddress'),
//            'return_number' =>  '1234561',
//            'Remark'        =>  I('Remark'),//备注
//            'GoodsName' =>  I('GoodsName'),//商品名称
//            'Goodsquantity' =>  I('Goodsquantity'),//商品数量
//            'GoodsPrice'    =>  I('GoodsPrice'),//商品价格
//            'GoodsDesc'     =>  I('GoodsDesc'),//商品描述
//            'IsReturnPrintTemplate' =>  '0',//返回电子面单模板：0-不需要；1-需要（如果调用批量打印则封装这个为0）
//            'returnjson' =>  1,//是否返回json格式
//        ];
        
        $res = curl_snatch($url,$data,$method='POST');
        
        
        print_r($res);
    }
    
    //批量打印的接口
    public function test_build_form(){
        //先通过快递鸟电子面单接口提交电子面单后，再组装POST表单调用快递鸟批量打印接口页面
        
        
        header("Content-type: text/html; charset=utf-8");
        
        if( C('IS_TEST') && 0 ){
            $url = C('YM_DOMAIN').'/api/kdniao/build_form';
        }
        else{
            $url = __APP__.'/api/kdniao/build_form';
        }
        
        
        $OrderCode = [
            '619589585151','619589585152'
        ];
        $PortName = '打印机名称一';
        
        $data = [
            'OrderCode' => $OrderCode,
            'PortName' => $PortName,
        ];
        
        $res = '<form id="form1" method="POST" action="' . $url . '"><input type="text" name="OrderCode" value="' . $OrderCode . '"/><input type="text" name="PortName" value="' . $PortName . '"/></form><script>form1.submit();</script>';
        
        //print_r($data);return;
        
        //$res = curl_snatch($url,$data,'POST');
        
        //$res = $this->sendPost($url,$data);
        
        print_r($res);
        
    }
    
    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
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
    
    
    public function accpet(){
      // 获取商品
//    $result = '{"client_id":"520e897572d61b4bff","id":"441333516","kdt_id":41495797,"mode":1,"msg":"%7B%22data%22%3A%22%7B%5C%22kdt_id%5C%22%3A41495797%2C%5C%22item_id%5C%22%3A441333516%2C%5C%22price%5C%22%3A1%2C%5C%22channel%5C%22%3A0%2C%5C%22alias%5C%22%3A%5C%222g2s24izovxhx%5C%22%2C%5C%22tax_class_code%5C%22%3A%5C%22%5C%22%2C%5C%22title%5C%22%3A%5C%22%E6%B5%8B%E8%AF%95%E5%95%86%E5%93%81%5C%22%2C%5C%22is_display%5C%22%3A1%7D%22%2C%22change_fields%22%3A%22%5B%5D%22%7D","sendCount":1,"sign":"f8ec0700771b8c99bb1de4074012672e","status":"ITEM_CREATE","test":false,"type":"ITEM_INFO","version":1540979244000}';
      // 商品删除
//    $result = '{"client_id":"520e897572d61b4bff","id":"441303855","kdt_id":41495797,"mode":1,"msg":"%7B%22data%22%3A%22%7B%5C%22kdt_id%5C%22%3A41495797%2C%5C%22item_id%5C%22%3A441303855%2C%5C%22goods_no%5C%22%3A%5C%22%5C%22%2C%5C%22channel%5C%22%3A0%2C%5C%22alias%5C%22%3A%5C%222fxwl68ipzjnp%5C%22%2C%5C%22tax_class_code%5C%22%3A%5C%22%5C%22%2C%5C%22title%5C%22%3A%5C%22%E6%B5%8B%E8%AF%95%E5%95%86%E5%93%81%5C%22%7D%22%2C%22change_fields%22%3A%22%5B%5D%22%7D","sendCount":0,"sign":"0e1835bfc3ca4b0cef3dbc2ffba47610","status":"ITEM_DELETE","test":false,"type":"ITEM_STATE","version":1540977436000}';
      // 新用户注册
//    $result = '{"client_id":"520e897572d61b4bff","id":"1065278884","kdt_id":41495797,"mode":1,"msg":"%7B%22account_id%22%3A%221065278884%22%2C%22account_type%22%3A%22YouZanAccount%22%2C%22created_at%22%3A1540975088%2C%22gender%22%3A0%2C%22is_member%22%3A0%2C%22mobile%22%3A%2217820728044%22%2C%22name%22%3A%22%22%2C%22src%22%3A100%7D","sendCount":1,"sign":"679822d251a3deb674b719961f25695a","status":"CUSTOMER_CREATED","test":false,"type":"SCRM_CUSTOMER_EVENT","version":1540976555753}';
      // 用户信息更新
//    $result = '{"client_id":"520e897572d61b4bff","id":"1065278884","kdt_id":41495797,"mode":1,"msg":"%7B%22account_id%22%3A%221065278884%22%2C%22account_type%22%3A%22YouZanAccount%22%2C%22birthday%22%3A%222016-10-31%22%2C%22mobile%22%3A%2217820728044%22%7D","sendCount":3,"sign":"b2ff750838a84297efed1bae080b5f27","status":"CUSTOMER_UPDATED","test":false,"type":"SCRM_CUSTOMER_EVENT","version":1540977099498}';
      // 新订单
//    $result = '{"client_id":"520e897572d61b4bff","id":"E20181101125859005500001","kdt_id":41495797,"kdt_name":"DMAE\u5b98\u65b9\u5546\u57ce","mode":1,"msg":"%7B%22order_promotion%22:%7B%22adjust_fee%22:%220.00%22%7D,%22refund_order%22:[],%22full_order_info%22:%7B%22address_info%22:%7B%22self_fetch_info%22:%22%22,%22delivery_address%22:%22%E8%81%9A%E8%81%9A%E5%95%8A%22,%22delivery_postal_code%22:%22%22,%22receiver_name%22:%22%E5%90%8E%22,%22delivery_province%22:%22%E5%8C%97%E4%BA%AC%E5%B8%82%22,%22delivery_city%22:%22%E5%8C%97%E4%BA%AC%E5%B8%82%22,%22delivery_district%22:%22%E4%B8%9C%E5%9F%8E%E5%8C%BA%22,%22address_extra%22:%22%7B%5C%22areaCode%5C%22:%5C%22110101%5C%22,%5C%22lon%5C%22:116.42282166241,%5C%22lat%5C%22:39.934442272263%7D%22,%22receiver_tel%22:%2213711283258%22%7D,%22remark_info%22:%7B%22buyer_message%22:%22%22%7D,%22pay_info%22:%7B%22outer_transactions%22:[],%22post_fee%22:%220.00%22,%22total_fee%22:%220.01%22,%22payment%22:%220.01%22,%22transaction%22:[]%7D,%22buyer_info%22:%7B%22outer_user_id%22:%22%22,%22buyer_phone%22:%2213711283267%22,%22fans_type%22:1,%22buyer_id%22:6616119,%22fans_id%22:6685786602,%22fans_nickname%22:%22Hino%22%7D,%22orders%22:[%7B%22outer_sku_id%22:%22%22,%22sku_unique_code%22:%22%22,%22goods_url%22:%22https:\/\/h5.youzan.com\/v2\/showcase\/goods%3Falias=3nhygvd463k7p%22,%22item_id%22:441402133,%22outer_item_id%22:%22%22,%22discount_price%22:%220.01%22,%22item_type%22:0,%22num%22:1,%22sku_id%22:0,%22sku_properties_name%22:%22[]%22,%22pic_path%22:%22https:\/\/img.yzcdn.cn\/upload_files\/2018\/11\/01\/Fow0rnSDfzNUkKWjdYO8wHUU7BdR.png%22,%22oid%22:%221480504542259315691%22,%22title%22:%22%E6%B5%8B%E8%AF%95%E4%BA%A7%E5%93%81%22,%22buyer_messages%22:%22%22,%22is_present%22:false,%22pre_sale_type%22:%22null%22,%22points_price%22:%220%22,%22price%22:%220.01%22,%22total_fee%22:%220.01%22,%22alias%22:%223nhygvd463k7p%22,%22payment%22:%220.01%22,%22is_pre_sale%22:%22null%22%7D],%22source_info%22:%7B%22is_offline_order%22:false,%22book_key%22:%22201811011258565bda8810aadbd35522%22,%22biz_source%22:%22null%22,%22source%22:%7B%22platform%22:%22wx%22,%22wx_entrance%22:%22direct_buy%22%7D%7D,%22order_info%22:%7B%22consign_time%22:%22%22,%22order_extra%22:%7B%22is_from_cart%22:%22false%22,%22is_member%22:%22false%22,%22buyer_name%22:%22Hino%22,%22is_points_order%22:%220%22,%22is_offline%22:%220%22%7D,%22created%22:%222018-11-01%2012:58:59%22,%22status_str%22:%22%E5%BE%85%E6%94%AF%E4%BB%98%22,%22expired_time%22:%222018-11-01%2013:58:59%22,%22success_time%22:%22%22,%22type%22:0,%22tid%22:%22E20181101125859005500001%22,%22confirm_time%22:%22%22,%22pay_time%22:%22%22,%22update_time%22:%222018-11-01%2012:58:59%22,%22pay_type_str%22:%22%22,%22is_retail_order%22:false,%22pay_type%22:0,%22team_type%22:0,%22refund_state%22:0,%22close_type%22:0,%22status%22:%22WAIT_BUYER_PAY%22,%22express_type%22:0,%22order_tags%22:%7B%22is_message_notify%22:true,%22is_secured_transactions%22:true%7D%7D%7D%7D","msg_id":"fa6d2e52-b216-499c-9623-e9217069a3b7","sendCount":0,"sign":"fcdf329fe285733d6779bffdc74f669b","status":"WAIT_BUYER_PAY","test":false,"type":"trade_TradeCreate","version":1541048339}';
      // 订单关闭取消
//    $result = '{"client_id":"520e897572d61b4bff","id":"E20181031225820078800029","kdt_id":41495797,"kdt_name":"DMAE\u5b98\u65b9\u5546\u57ce","mode":1,"msg":"%7B%22update_time%22:%222018-10-31%2023:02:19%22,%22close_reason%22:%22refund,%20order%20closed!%22,%22tid%22:%22E20181031225820078800029%22,%22close_type%22:2%7D","msg_id":"d86cbe5c-b1bb-486f-89a5-11e4bb65732e","sendCount":0,"sign":"d1f4f50b8dc2fe82f5fa590a67d49773","status":"TRADE_CLOSED","test":false,"type":"trade_TradeClose","version":1540998186}';
//    $result = '{"client_id":"520e897572d61b4bff","id":"1062791115","kdt_id":41495797,"mode":1,"msg":"%7B%22account_id%22%3A%221062791115%22%2C%22account_type%22%3A%22YouZanAccount%22%2C%22mobile%22%3A%2215279688888%22%7D","sendCount":3,"sign":"40d9d1d44512972b5287ddf2dd92fb53","status":"CUSTOMER_UPDATED","test":false,"type":"SCRM_CUSTOMER_EVENT","version":1540954744995}';
//    $result = json_decode($result,true);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
      $mode = 1; //服务商类型的消息
      import('Lib.Action.Api','App');
      $Api = new Api();
      
      $is_test = C('IS_TEST');
      if(!$is_test){
        $result = $GLOBALS['HTTP_RAW_POST_DATA'];
      }
      $result = json_decode($result,true);
      setLog(json_encode($result));
      /**
        *  判断是否为心跳检查消息，1.是则直接返回
      */
      if($result['test']){
        $return_result = [
          'code' => 0,
          'msg'  => 'success'
        ];
        $this->ajaxReturn($return_result);
      }
      /**
        * 解析消息推送的模式  这步判断可以省略
        * 0-商家自由消息推送 1-服务商消息推送
        * 以服务商举例,判断是否为服务商类型的消息,否则直接返回
      */
     
      if($result['mode'] != $mode){
        $return_result = [
          'code' => 0,
          'msg'  => 'success'
        ];
        setLog('消息推送类型错误：'.json_encode($result),'error_mode');
        $this->ajaxReturn($return_result);
      }
      $check_sign = $Api->check_sign($result['sign'],$result['msg']);
      if($check_sign['code'] == 1){
        $data = json_decode(urldecode($result['msg']),true);
//      $data = json_decode($data['data'],true);
        $res = $Api->check_type($result['type'],$result['status'],$data);
        if($res['code'] != 1){
          setLog('数据更改失败：'.$res['msg'],'error_accpet');
        }
      }else{
        setLog('签名错误：'.$result,'error_sign');
      }
      $return_result = [
        'code' => 0,
        'msg'  => 'success'
      ];
      $this->ajaxReturn($return_result);
    }
    
    
    
    
    public function upgrade_order_logistics(){
      import('Lib.Action.Mmtapi','App');
      $Mmtapi = new Mmtapi();
      import('Lib.Action.Order','App');
      $Order = new Order();
      
      $result = $GLOBALS['HTTP_RAW_POST_DATA'];
//    $result = '{"tenantid":"000078","tenantcode":"jxmb","fnname":"","apiurl":"","topic":"ecp.scm.b2corder.send","msgid":"5ba0c91ec88dd212b84fa3b5","timestamp":1537263902528,"content":"{\"tid\":\"931537250914914\",\"expresscode\":\"SF\",\"expressname\":\"\u987a\u4e30\u901f\u8fd0\",\"expressno\":\"249406008318\"}","sign":"195b49614c402774dcb16f8358aa2807"}';
      $result = json_decode($result,true);

      $current_sign = $Mmtapi->check_sign($result['sign'],$result['content']);
      
      if($current_sign['code'] != 1){
        setLog('异步回调签名验证失败：'.json_encode($result),'error_sign');
        $return_result = [
          'success' => 'false',
          'desc' => $current_sign['msg'],
        ];
        $this->ajaxReturn($return_result);
      }
      $result['content'] = json_decode($result['content'],true);
      $order_num = $result['content']['tid'];
      $shipping_num = $result['content']['expressno'];
      $shipping_code = $result['content']['expresscode'];
      $shipping_name = $result['content']['expressname'];
      
      if(!empty($order_num)){
//      $audit_res = $Order->radmin_audit($order_num);
//      if($audit_res['code'] != 1){
//        setLog('异步回调审核订单信息失败：'.$order_num.' : '.json_encode($audit_res),'error_audit_order');
//        $return_result = [
//          'success' => 'false',
//          'desc' => '审核订单信息失败！',
//        ];
//        $this->ajaxReturn($return_result);
//      }
        $map = [
          'order_num' => $order_num
        ];
        $is_exsit = M('order')->where($map)->find();
        if($is_exsit){
          $data = [
            'shipper' => $shipping_code,
            'ordernumber' => $shipping_num,
            'status' => 2
          ];
          $res = M('order')->where($map)->save($data);
          if($res){
            $return_result = [
              'success' => 'true',
              'desc' => '成功',
            ];
            $this->ajaxReturn($return_result);
          }else{
            setLog('异步回调更新订单信息失败：'.json_encode($result),'error_upgrade_order');
            $return_result = [
              'success' => 'false',
              'desc' => '更新订单信息失败！',
            ];
            $this->ajaxReturn($return_result);
          }
        }else{
          setLog('异步回调更新订单信息失败：'.json_encode($result),'error_upgrade_order');
          $return_result = [
            'success' => 'false',
            'desc' => '无该订单信息！',
          ];
          $this->ajaxReturn($return_result);
        }
      }
      
    }
    
}

?>