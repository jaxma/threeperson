<?php
//微信模板消息推送
header("Content-Type: text/html; charset=utf-8");


class Newredis {
    
    private $is_open = TRUE;//是否开启
    
    private $default_expire = 10;//默认缓存时间，0为不过期

    public $newredis;//redis
    
    private $connect_type = 1;//连接方法

    //缓存名称（为防止缓存设置混乱，导致重复名字，缓存名称都在这里设置一次）
    public $cache_key = [
        'test','test1','test777'
    ];
    
    
    /**
     * 架构函数
     */
    public function __construct() {
        
        //如果关闭直接返回false
        if( !$this->is_open ){
            return FALSE;
        }
        
        $this->newredis = new Redis();
    }
    
    //连接redis
    public function connect($expire_time=NULL){
        
        if( $expire_time === NULL ){
            $expire_time = $this->default_expire;
        }
        
        
        if( $this->connect_type == 2 ){
            //当使用pconnect时，连接会被重用，连接的生命周期是fpm进程的生命周期，而非一次php的执行。 
            return $this->newredis->pconnect('127.0.0.1','6379', $expire_time);
        }
        else{
            //默认的连接，每次使用自动关闭
            return $this->newredis->connect('127.0.0.1','6379', $expire_time);
        }
    }
    
    
    //--------------------start 一般操作---------------------
    
    /**
     * 缓存数据
     * @param string $key          //缓存名字
     * @param string $value         //缓存数据
     * @param int $expire_time      //生存时间
     * @param string $key_suffix    //变动的key名后缀
     * @return type
     */
    public function set($key,$value,$expire_time=NULL,$key_suffix=NULL){
        
        if( !in_array($key, $this->cache_key) ){
            return FALSE;
        }
        
        $key = $key.$key_suffix;
        
        $this->connect($expire_time);
        
        //判断写入值有没重复
//        $repeat_result = $this->newredis->setnx($key,$value);
//        if( $repeat_result ){
//            return FALSE;
//        }
        
        return $this->newredis->set($key,$value);
    }
    
    /**
     * 在某个缓存直接增加值
     * @param type $key
     * @param type $value
     * @param type $path            //right/left：右边/左边
     * @param type $expire_time
     * @return boolean
     */
    public function push($key,$value,$path='right',$expire_time=NULL,$key_suffix=NULL){
        
        if( !in_array($key, $this->cache_key) ){
            return FALSE;
        }
        
        $key = $key.$key_suffix;
        
        if( !in_array($path, ['right','left']) ){
            return FALSE;
        }
        
        $this->connect($expire_time);
        
        if( $path == 'left' ){
            return $this->newredis->lPush($key,$value);
        }
        else{
            return $this->newredis->rPush($key,$value);
        }
    }
    
    
    //输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
    public function pop($key,$path='right'){
        
        if( !in_array($path, ['right','left']) ){
            return FALSE;
        }
        
        $this->connect();
        
        if( $path == 'left' ){
            return $this->newredis->lPop($key);
        }
        else{
            return $this->newredis->rPop($key);
        }
    }
    
    
    //同时设置多个不同key的缓存
    public function mset(){
        
    }
    
    /**
     * 获取缓存值
     * @param string $key
     * @param int $sleeptime    //sleep时间，如果有返回值并且立刻拿的缓存值为空的，可以延时再拿
     * @return string
     */
    public function get($key,$sleeptime=NULL){
        $this->connect();
        $res = $this->newredis->get($key);
        
        //如果不存在并且$sleeptime有值
        if( !$res && !empty($sleeptime) && is_numeric($sleeptime) ){
            sleep($sleeptime);
            $res = $this->newredis->get($key);
        }
        
        return $res;
    }
    
    /**
     * 删除某个（多个）指定缓存
     * @param string|array $key     //可以以数组形式删除多个
     * @return int
     */
    public function delete($key){
        $this->connect();
        return $this->newredis->delete($key);
    }
    
    
    
    
    
    
    //得到某个缓存的生存时间
    public function ttl($key){
        $this->connect();
        return $this->newredis->ttl($key);
    }
    
    //获取长度
    public function size($key){
        $this->connect();
        return $this->newredis->lSize($key);
    }
    
    
    
    //--------------------end 一般操作---------------------
    
    
    
    
    //--------------------start 事务操作---------------------
    
    
    //--------------------end 事务操作---------------------
    
}