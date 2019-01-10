  <?php
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class VideoAction extends Action {

    //
    public function index() {
        
    }
    
    public function video() {
        $key = I('key');
        if(md5($key) != 'b0d5ecaf75116cf41c5c7f416303c5b7' || !$key || empty($key)){
          $this->ajaxReturn(array('state'=>101,'msg'=>'非法请求'));
          return false;
        }
        $where = 'isopen = 1';
        $page_num = I('page_num');
        $page_list_num = 3;
        $list = array();
        $count = M('video')->where($where)->count();
        if( $count > 0 ){
            if( !empty($page_num) ){
                $page_con = $page_num.','.$page_list_num;
                $list = M('video')->where($where)->order('sequence desc')->page($page_con)->select();
                foreach ($list as $k => $v) {
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                }
            }else{
                $list = M('video')->where($where)->order('sequence desc')->select();
                foreach ($list as $k => $v) {
                  $list[$k]['time'] = date('Y-h-m h:i',$v['time']);
                }
            }
        }
        $res = [
          'state'=>1,
          'msg'=>'数据获取成功',
          'count'=>$count,
          'limit'=>$page_num,
          'list'=>$list,
        ];
        $this->ajaxReturn($res);
    }
    
    
}

?>