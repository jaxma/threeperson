<?php

/*
 * //类的公共方法
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Common
 *
 * @author Administrator
 */
class Common {

    /**
     * 根据现有查询出来的数据($data)连表查询另一个/多个表
     * @param type $data 要连表的数据
     * @param type $rel_tabel_name 要连表的表名
     * @param type $rel_field 关联字段
     * @param type $search_id   
     * @return string
     */
    public function get_related_data($data, $rel_tabel_name, $rel_field,$search_id='id') {
        if (!$data) {
            return;
        }
        $ids = [];//关联字段的数据集合
        $rel_data = [];//连表数据整合数组
        if (!is_array($rel_field)) {
            //一个关联字段
            foreach ($data as $v) {
                $ids[] = $v[$rel_field];
            }
        } else {
            //多个个关联字段
            foreach ($rel_field as $field) {
                foreach ($data as $v) {
                     $ids[] = $v[$field];
                }
            }
        }
        array_unique($ids);
        if ($ids) {
            //查询得到连表数据
            $rel_info = M($rel_tabel_name)->where([$search_id => ['in', $ids]])->select();
            
            //因为代理表使用太多，增加判断用于显示总部
            if( $rel_tabel_name == 'distributor' ){
                $rel_info[] = [
                    'id'    =>  0,
                    'name'  =>  '总部',
                    'wechatnum' =>  '总部',
                    'levname'   =>  '总部',
                ];
            }
            
            //整合数据
            foreach ($rel_info as $info) {
                $rel_data[$info[$search_id]] = $info;
            }
            //连表查询的数据整合进最终的数组中返回
            if (!is_array($rel_field)) {
                foreach ($data as $k => $v) {
                    $data[$k][$rel_field.'_info'] = $rel_data[$v[$rel_field]];
                }
            } else {
                foreach ($rel_field as $field) {
                    foreach ($data as $k => $v) {
                         $data[$k][$field.'_info'] = $rel_data[$v[$field]];
                     } 
                }
            }
        }
        return $data;
    }
}
