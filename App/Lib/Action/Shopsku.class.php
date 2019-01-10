<?php

/**
 * 商品属性/库存相关处理代码
 */

class Shopsku {
    
    private $templet;
	  private $oldPropertyPrices;
    private $sku_model;
    private $level_name;
    private $templet_model;
    private $property_model;
    private $to_property_model;
    private $property_to_value_model;
    private $property_value_model;


    public function __construct() {
        $this->sku_model = M('shop_templet_sku');
        $this->level_name = C('LEVEL_NAME');
        $this->templet_model = M('shop_templet');
        $this->property_model = M('shop_templet_property');
        $this->to_property_model = M('shop_templet_to_property');
        $this->property_to_value_model = M('shop_templet_property_to_value');
        $this->property_value_model = M('shop_templet_property_value');
    }


    /****************************后台代码***********************************/
    
    //获取属性组合
	function get_properties_value_combination($templet) {
		$this->templet = $templet;
		$this->oldPropertyPrices = $this->templet['propertyPrices'];
//		var_dump($this->oldPropertyPrices);die;
		$this->templet['propertyPrices'] = [];
		$this->linear($this->templet['properties']);
        return $this->templet;
	}
    
    //保存商品属性/属性值关系和库存
    function save_templet_info($templet, $templet_id, $show_stock = false) {
        $properties = $templet['properties'];
        $property_prices = $templet['propertyPrices'];
        if (!$show_stock) {
            $this->save_templet_to_property($properties, $templet_id);
        
            if ($properties = $this->save_values($properties, $templet_id)) {
                $this->sku_model->where(['templet_id' => $templet_id])->delete();
            }
        }
        $this->save_prices($properties, $property_prices, $templet_id, $show_stock);
    }
    
    //保存商品和属性关系
    private function save_templet_to_property($properties, $templet_id) {
		if (empty($properties)) {
			return;
		}
        $this->to_property_model->where(['templet_id' => $templet_id])->delete();
		foreach ($properties as $property) {
            $data = [
               'templet_id' => $templet_id,
               'pid' => $property['id']
            ];
            $this->to_property_model->add($data);
		}
	}
    
    //保存属性和属性值
    private function save_values($properties, $templet_id) {
		if (empty($properties)) {
			return false;
		}
		$this->property_to_value_model->where(['templet_id' => $templet_id])->delete();
		foreach ($properties as $i=>&$property) {
			$vidLeft = [];
			foreach ($property['values'] as $j=>&$value) {
				$vidLeft[] = $this->save_property_value($property, $value);
				$this->save_templet_to_property_value($templet_id, $property, $value);
			}
//			if (!empty($vidLeft)) {
//				ProductPropertyValue::deleteAll(['and', 'pid=:pid', ['not in', 'vid', $vidLeft]], ['pid'=>$property['id']]);
//			}
		}
//		$this->trigger(self::EVENT_AFTER_SAVE_PROPERTY_VALUES);
        return $properties;
	}
    
    //保存属性值
    private function save_property_value($property, &$value) {
		if (isset($value['id'])) {
			return $value['id'];
		}
        $data = [
            'pid' => $property['id'],
            'value' => $value['value'],
        ];
        $res = $this->property_value_model->add($data);
		$value['id'] = $res;
		return $value['id'];
	}
    
    //保存属性，属性值之间的关系
    private function save_templet_to_property_value($templet_id, $property, $value) {
        $data = [
            'templet_id' => $templet_id,
            'pid' => $property['id'],
            'vid' => $value['id']
        ];
        $this->property_to_value_model->add($data);
	}
    
     /**
     * 保存库存/价格
     * @param type $properties
     * @param type $propertyPrices
     * @param string $valueKey
     * @param string $valueIdKey
     */
    private function save_prices($properties, $propertyPrices, $templet_id, $show_stock, $valueKey = '', $valueIdKey = '') {
		$property = array_shift($properties);
		if ($valueKey !== '' && !empty($property)) {
			$valueKey .= ';';
			$valueIdKey .= ';';
		}
		$pid = $property['id'];
		foreach ($property['values'] as $value) {
			$vKey = $valueKey."$pid:{$value['value']}";
			$vidKey = $valueIdKey."$pid:{$value['id']}";
			if (!empty($properties)) {
				$this->save_prices($properties, $propertyPrices, $templet_id, $show_stock, $vKey, $vidKey);
				continue;
			} else {
				$this->saveSku($templet_id, $vidKey, $propertyPrices[$vKey], $show_stock);
			}
		}
	}
    
    //库存/价格
    private function saveSku($templet_id, $properties, $sku, $show_stock) {
        $where = [
            'templet_id' => $templet_id,
            'properties' => $properties,
        ];

        $data = [
            'quantity' => $sku['stock'],
            'price' => $sku['price'],
            'image' => $sku['image'],
            'bind_product' => $sku['bind_product'],
        ];

        $data = $this->get_other_prices($sku, $data);

        if (!$show_stock) {
            return $this->sku_model->add(array_merge($data, $where));
        } else {
            return $this->sku_model->where($where)->save($data);
        }
	}


    private function linear($properties, $nameKey = '') {
		if (empty($properties)) {
			return;
		}
		$property = array_shift($properties);
		if ($nameKey !== '' && !empty($property)) {
			$nameKey .= ';';
		}
		$id = $property['id'];
		foreach ($property['values'] as $v) {
			$key = $nameKey."$id:$v[value]";
			if (!empty($properties)) {
				$this->linear($properties, $key);
				continue;
			}
			if (isset($this->oldPropertyPrices[$key])) {
				$this->templet['propertyPrices'][$key] = $this->oldPropertyPrices[$key];
			} else {
				$this->templet['propertyPrices'][$key] = ['price'=>0, 'stock'=>1];
			}
		}
	}
    
    //编辑商品属性库存相关函数
    public function init_properties($templet) {
        $sku = $this->sku_model->where(['templet_id' => $templet['id']])->find();
		if (empty($sku)) {
			return;
		}
		$properties = $this->get_properties($templet['id']);
		if (empty($properties)) {
			return;
		}
		$properties = $this->get_values($templet['id'], $properties);
        
		$pp = $this->get_prices($templet['id']);
//		var_dump($pp);die;
		if (empty($pp['prices'])) {
			unset($properties);
			$templet['has_property'] = 0;
			return;
		}
        
        $result = [
            'properties' => $properties,
            'propertyPrices' => $pp['prices'],
            'price' => $pp['price'],
            'stock' => $pp['totalStock']
        ];
        return $result;
	}
    
    private function get_prices($templet_id) {
		$skus = $this->sku_model->where(['templet_id' => $templet_id])->select();
		$prices = [];
		$totalStock = 0;
		$price = 0;
        $values = [];
		$property_values = $this->property_value_model->where(['id'=>['in', $this->get_vids($skus)]])->select();
        foreach ($property_values as $value) {
            $values[$value['id']] = $value;
        }
		foreach ($skus as $sku) {
			$break = false;
			$sku['properties'] = preg_replace_callback('/(\d+);/', function($matches) use ($values, &$break) {
				if (!isset($values[$matches[1]])) {  //遇到这种情况，需要在保存时清空掉此产品所有的商品属性
					$break = true;
					return '';
				}
				return $values[$matches[1]]['value'].';';
			}, $sku['properties'].';');
			if ($break) {
				$prices = [];
				break;
			}
			$sku['properties'] = substr($sku['properties'], 0, -1);
			
			$prices[$sku['properties']] = $this->get_other_prices($sku, ['price'=>$sku['price'], 'stock'=>$sku['quantity'], 'bind_product' =>$sku['bind_product'],'image' => $sku['image']]);
			$price = $sku['price'];
			$totalStock += $sku['quantity'];
		}
		return ['totalStock'=>$totalStock, 'price'=>$price, 'prices'=>$prices];
	}
    
    private function get_vids($skus) {
		$vids = [];
		foreach ($skus as $sku) {
			$pvs = explode(';', $sku['properties']);
			foreach ($pvs as $pv) {
				$vids[] = explode(':', $pv)[1];
			}
		}
		return $vids;
	}
    
    private function get_values($templet_id, $properties) {
        $property_value_model = $this->property_value_model;
		$vs = $this->property_to_value_model->where(['templet_id' => $templet_id])->select();
		$values = [];
		foreach ($vs as $v) {
			if (!isset($properties[$v['pid']]['values'])) {
				$properties[$v['pid']]['values'] = [];
			}
			$value =  $property_value_model->find($v['vid']);
			$properties[$v['pid']]['values'][$value['value']] = ['id'=>$v['vid'], 'value'=>$value['value']];
		}
		return $properties;
	}
    
    private function get_properties($templet_id) {
        $property_model = $this->property_model;
		$ps = $this->to_property_model->where(['templet_id' => $templet_id])->select();
		$props = [];
		foreach ($ps as $p) {
			$props[$p['pid']] = ['id'=>$p['pid'], 'name'=> $property_model->where(['id' => $p['pid']])->getField('name')];
		}
		return $props;
	}
    
    
    /******************************************前端代码**********************************************/
    
    //
    public function get_templet_skus($templet_id) {
        return $this->sku_model->where(['templet_id' => $templet_id])->select();
    }
    
    
    public function get_templet_sku($id) {
        return $this->sku_model->where(['id' => $id])->find();
    }
    
    //获取库存ID数组
    public function get_templet_sku_ids($ids) {
        array_unique($ids);
        return $this->sku_model->where(['id' => array('in',$ids)])->select();
    }
    
    //检查库存
    public function check_templet_quantity($sku_info, $sku_id, $tempelt_id, $num) {
        //没有属性
        if (!$sku_id) {
            $templet = $this->templet_model->find($tempelt_id);
            if($templet['quantity'] < $num) {
                return false;
            }
        } else {
            if($sku_info['id'] != $sku_id) {
                return false;
            }
            if($sku_info['templet_id'] != $tempelt_id) {
                return false;
            }
            if($sku_info['quantity'] < $num) {
                return false;
            }
        }
        return true;
    }


    //下单后改变商品库存/销量
     public function change_quantity_and_sales($sku_id, $templet_id, $num, $type='dec') {
        if ($type == 'dec') {
            if ($sku_id) {
                //有商品属性
                $this->sku_model->where(['id' => $sku_id])->setDec('quantity', $num);
                $this->sku_model->where(['id' => $sku_id])->setInc('sales', $num);
            }
            $this->templet_model->where(['id' => $templet_id])->setDec('quantity', $num);
            $this->templet_model->where(['id' => $templet_id])->setInc('sales', $num);
        } else if ($type == 'inc') {
            if ($sku_id) {
                //有商品属性
                $this->sku_model->where(['id' => $sku_id])->setInc('quantity', $num);
                $this->sku_model->where(['id' => $sku_id])->setDec('sales', $num);
            }
            $this->templet_model->where(['id' => $templet_id])->setInc('quantity', $num);
            $this->templet_model->where(['id' => $templet_id])->setDec('sales', $num);
        }
    }

        /**
     * 获取商品属性/库存价格信息
     * @param type $templet_id
     * @return type
     */
    public function get_templet_properties($templet_id) {
        $skus = $this->get_templet_skus($templet_id);
        if (empty($skus)) {
            return null;
        }
        $properties = D('')->field('`tp`.`id`,`tp`.`name`')->table(['shop_templet_to_property'=>'ttp','shop_templet_property'=>'tp'])
                ->where("`ttp`.`templet_id`='" . $templet_id . "' AND `tp`.`id`=`ttp`.`pid`")->order('`ttp`.`id` ASC')->select();
        
        if (!empty($properties)) {
            $property_values = D('')->field('`tpv`.`id`,`tpv`.`value`,`tpv`.`pid`')
                    ->table(['shop_templet_property_to_value'=>'tptv','shop_templet_property_value'=>'tpv'])
                    ->where("`tptv`.`templet_id`='" . $templet_id . "' AND `tpv`.`id`=`tptv`.`vid`")->order('`tptv`.`id` ASC')->select();
        }
        
        $new_property_values = [];
        if (!empty($property_values)) {
            foreach ($property_values as $value) {
//                foreach ($skus as $key => $val) {
                    $new_property_values[$value['pid']][] = [
                        'vid'=>$value['id'],
                        'value'=>$value['value'],
                    ];
//                }
//                unset($skus[$key]);
//                break;
            }
        }
        
        foreach($properties as $value){
            $news_properties[] = [
                'pid'=>$value['id'],
                'name'=>$value['name'],
                'values'=>$new_property_values[$value['id']],
            ];
        }
        return $news_properties;
    }
    
    //根据库存id获取属性组合
    public function get_templet_property_com($sku_id) {
        return $this->sku_model->where(['id' => $sku_id])->getField('properties');
    }
    
    public function get_value($property) {
        $name = '';
        $coms = explode(';', $property);
        foreach ($coms as $com) {
            $value = explode(':', $com);
            $vids[] = (int)$value[1];
        }
        if ($vids) {
            $values = $this->property_value_model->where(['id' => ['in', $vids]])->select();
            
            foreach ($values as $v) {
                $name.= $v['value'] . ' ';
            }
        }
        return $name;
    }

    /**
     * 获取其它代理级别价格
     * @param array $data 价格数据
     * @param array $merge 要合并的数组
     */
    public function get_other_prices($data, $merge) {
        $other = [];
        foreach ($this->level_name as $k => $v) {
            $other["price$k"] = $data["price$k"];
        }
        return array_merge($merge, $other);
    }
    
    //判断sku_id是否存在并且返回
    public function is_null_sku_id($sku_ids) {
        $null_ids = [];
        if (is_string($sku_ids)) {
            $sku_ids = explode('|', $sku_ids);
        }
        foreach ($sku_ids as $id) {
            if (!$this->sku_model->find($id)) {
                $null_ids[] = $id;
            }
        }
        return array_unique($null_ids);
    }
    
    //删除产品后删除相关属性/库存
    public function delete_properties($templet_id) {
        $vid = [];
        $this->sku_model->where(['templet_id' => $templet_id])->delete();
        $this->to_property_model->where(['templet_id' => $templet_id])->delete();
        $properties = $this->property_to_value_model->where(['templet_id' => $templet_id])->select();
        foreach ($properties as $v) {
            $vid[] = $v['vid'];
        }
        $this->property_value_model->where(['id' => ['in', $vid]])->delete();
        $this->property_to_value_model->where(['templet_id' => $templet_id])->delete();
        
    }
    
    public function get_templet_info ($order_info){
      $s_pid = $order_info['p_id'];
      if(empty($s_pid)){
        $result = [
          'code' => 2,
          'msg' => '参数有误！',
          'info' => null
        ];
      }else{
        $shop_product = M('shop_templet')->where(['id'=>$s_pid])->find();
        $bind_pid = $shop_product['bind_pid'];
        if(!empty($shop_product['bind_property'])){
            $bind_property = $shop_product['bind_property'];
            $sku_ids = null;
            $sku_info = M('templet_sku')->where(['template_id'=>$bind_pid,'properties'=>$bind_property])->getField('id');
            if($sku_info){
              $sku_ids[] = $sku_info;
            } else{
              $sku_ids[] = "";
            }
        }
        if($shop_product['has_property'] == 1 ){
            $property_info = M('shop_templet_sku')->where(['id'=>$order_info['sku_id']])->find();
            if($property_info && !empty($property_info['bind_product'])){
                $bind_product = $property_info['bind_product'];
                $bind_product = explode(' ',$bind_product);
                $bind_pid = $bind_product[0];
                $temp_property = explode(';',$bind_product[1]);
                $bind_property = '';
                foreach($temp_property as $k => $v){
                  $v = explode(':',$v);
                  array_pop($v);
                  $bind_property .= implode(':',$v);
                  if($temp_property[$k+1] != null){
                    $bind_property .= ';';
                  }
                }
                $sku_ids = null;
                $sku_info = M('templet_sku')->where(['template_id'=>$bind_pid,'properties'=>$bind_property])->getField('id');
                if($sku_info){
                  $sku_ids[] = $sku_info;
                } else{
                  $sku_ids[] = "";
                }
            }
          }
        $bind_info = [
          'sku_ids' => $sku_ids,
          'bind_pid' => $bind_pid
        ];
        $result = [
          'code' => 1,
          'msg' => '获取成功！',
          'info' => $bind_info
        ];
      }
      return $result;
    }
}
