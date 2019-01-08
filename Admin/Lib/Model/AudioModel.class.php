<?php

class AudioModel extends Model {
	/**
	 * 获得指定分类下的子分类的数组
	 *
	 * @access  public
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $selected   当前选中分类的ID
	 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
	 * @param   int     $level      限定返回的级数。为0时返回所有级数
	 * @return  mix
	 */
	function attr_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
	{
		static $res = NULL;

		if ($res === NULL)
		{
			$sql = "SELECT c.*,c.attr_id AS cat_id, COUNT(s.attr_id) AS has_children".
			// ", COUNT(a.article_id) AS aricle_num ".
			   " FROM " . C('DB_PREFIX') . "audioattr_list AS c".
			   " LEFT JOIN " . C('DB_PREFIX') . "audioattr_list AS s ON s.parent_id=c.attr_id".
			   // " LEFT JOIN " . C('DB_PREFIX') . "article AS a ON a.cat_id=c.cat_id".
			   " GROUP BY c.attr_id ".
			   " ORDER BY parent_id, sort_order ASC";
			$model = M("audioattr_list");
			
			$res = $model->query($sql);
		}
		
		if (empty($res) == true)
		{
			return $re_type ? '' : array();
		}
		
		$options = article_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组
		
		/* 截取到指定的缩减级别 */
		if ($level > 0)
		{
			if ($cat_id == 0)
			{
				$end_level = $level;
			}
			else
			{
				$first_item = reset($options); // 获取第一个元素
				$end_level  = $first_item['level'] + $level;
			}

			/* 保留level小于end_level的部分 */
			foreach ($options AS $key => $val)
			{
				if ($val['level'] >= $end_level)
				{
					unset($options[$key]);
				}
			}
		}
		
		

		$pre_key = 0;
		foreach ($options AS $key => $value)
		{
			$options[$key]['has_children'] = 1;
			if ($pre_key > 0)
			{
				if ($options[$pre_key]['attr_id'] == $options[$key]['parent_id'])
				{
					$options[$pre_key]['has_children'] = 1;
				}
			}
			$pre_key = $key;
		}
		
		if ($re_type == true)
		{
			$select = '';
			foreach ($options AS $var)
			{
				$select .= '<option value="' . $var['attr_id'] . '" ';
				// $select .= ' cat_type="' . $var['cat_type'] . '" ';
				$select .= ($selected == $var['attr_id']) ? "selected='ture'" : '';
				$select .= '>';
				if ($var['level'] > 0)
				{
					$select .= str_repeat('&nbsp;', $var['level'] * 4);
				}
				$select .= htmlspecialchars(addslashes($var['attr_name'])) . '</option>';
			}

			return $select;
		}
		else
		{
			return $options;
		}
	}
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function get_attr_categories_tree($cat_id = 0)
	{
		$M_attr_list=M('audioattr_list');
		if ($cat_id > 0)
		{
			$parentCat=$M_attr_list->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
			$parent_id = $parentCat['parent_id'];
		}
		else
		{
			$parent_id = 0;
		}

		/*
		 判断当前分类中全是是否是底级分类，
		 如果是取出底级分类上级分类，
		 如果不是取当前分类及其下的子分类
		*/
		$sameGradeCount=$M_attr_list->where(array('parent_id'=>$parent_id))->count();
		
		
		if ($sameGradeCount || $parent_id == 0)
		{
			
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT attr_id AS cat_id,attr_name ,parent_id,sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'audioattr_list ' .
					"WHERE parent_id = '$parent_id' ORDER BY sort_order ASC, attr_id ASC";

			$res = $M_attr_list->query($sql);


			foreach ($res AS $row)
			{
				
				$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
				$cat_arr[$row['cat_id']]['name'] = $row['attr_name'];
				$cat_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
				if (isset($row['cat_id']) != NULL)
				{
					$cat_arr[$row['cat_id']]['cat_id'] = self::get_attr_child_tree($row['cat_id']);
				}
				
			}
		}
		if(isset($cat_arr))
		{
			return $cat_arr;
		}
	}

	function get_attr_child_tree($tree_id = 0)
	{
		$M_attr_list=M('audioattr_list');
		$three_arr = array();
		$sameGradeCount=$M_attr_list->where(array('parent_id'=>$tree_id))->count();
		if ($sameGradeCount || $tree_id == 0)
		{
			$child_sql = 'SELECT attr_id  AS cat_id, attr_name, parent_id ,sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'audioattr_list ' .
					"WHERE parent_id = '$tree_id' ORDER BY sort_order ASC, cat_id ASC";
			$res = $M_attr_list->query($child_sql);
			foreach ($res AS $row)
			{
			   $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
			   $three_arr[$row['cat_id']]['name'] = $row['attr_name'];
				$three_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
			   if (isset($row['cat_id']) != NULL)
				{
					   $three_arr[$row['cat_id']]['cat_id'] = get_article_child_tree($row['cat_id']);
				}
			}
		}
		return $three_arr;
	}
	
	
	
	
	
	/**
	 * 获得指定分类下的子分类的数组
	 *
	 * @access  public
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $selected   当前选中分类的ID
	 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
	 * @param   int     $level      限定返回的级数。为0时返回所有级数
	 * @return  mix
	 */
	function cat_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
	{
		static $res = NULL;

		if ($res === NULL)
		{
			$sql = "SELECT c.*,c.cat_id AS cat_id, COUNT(s.cat_id) AS has_children".
			// ", COUNT(a.article_id) AS aricle_num ".
			   " FROM " . C('DB_PREFIX') . "audiocat_list AS c".
			   " LEFT JOIN " . C('DB_PREFIX') . "audiocat_list AS s ON s.parent_id=c.cat_id".
			   // " LEFT JOIN " . C('DB_PREFIX') . "article AS a ON a.cat_id=c.cat_id".
			   " GROUP BY c.cat_id ".
			   " ORDER BY parent_id, sort_order ASC";
			$model = M("audiocat_list");
			
			
			$res = $model->query($sql);
		}
		
		if (empty($res) == true)
		{
			return $re_type ? '' : array();
		}
		
		$options = article_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组
		// print_r($res);
		// exit();
		/* 截取到指定的缩减级别 */
		if ($level > 0)
		{
			if ($cat_id == 0)
			{
				$end_level = $level;
			}
			else
			{
				$first_item = reset($options); // 获取第一个元素
				$end_level  = $first_item['level'] + $level;
			}

			/* 保留level小于end_level的部分 */
			foreach ($options AS $key => $val)
			{
				if ($val['level'] >= $end_level)
				{
					unset($options[$key]);
				}
			}
		}
		
		

		$pre_key = 0;
		foreach ($options AS $key => $value)
		{
			$options[$key]['has_children'] = 1;
			if ($pre_key > 0)
			{
				if ($options[$pre_key]['cat_id'] == $options[$key]['parent_id'])
				{
					$options[$pre_key]['has_children'] = 1;
				}
			}
			$pre_key = $key;
		}
		
		if ($re_type == true)
		{
			$select = '';
			foreach ($options AS $var)
			{
				$select .= '<option value="' . $var['cat_id'] . '" ';
				// $select .= ' cat_type="' . $var['cat_type'] . '" ';
				$select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
				$select .= '>';
				if ($var['level'] > 0)
				{
					$select .= str_repeat('&nbsp;', $var['level'] * 4);
				}
				$select .= htmlspecialchars(addslashes($var['cat_name'])) . '</option>';
			}

			return $select;
		}
		else
		{
			return $options;
		}
	}
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function get_cat_categories_tree($cat_id = 0)
	{
		$M_cat_list=M('audiocat_list');
		if ($cat_id > 0)
		{
			$parentCat=$M_cat_list->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
			$parent_id = $parentCat['parent_id'];
		}
		else
		{
			$parent_id = 0;
		}

		/*
		 判断当前分类中全是是否是底级分类，
		 如果是取出底级分类上级分类，
		 如果不是取当前分类及其下的子分类
		*/
		$sameGradeCount=$M_cat_list->where(array('parent_id'=>$parent_id))->count();
		
		
		if ($sameGradeCount || $parent_id == 0)
		{
			
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT cat_id AS cat_id,cat_name ,parent_id,sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'audiocat_list ' .
					"WHERE parent_id = '$parent_id' ORDER BY sort_order ASC, cat_id ASC";

			$res = $M_cat_list->query($sql);

			foreach ($res AS $row)
			{
				
				$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
				$cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
				$cat_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
				if (isset($row['cat_id']) != NULL)
				{
					$cat_arr[$row['cat_id']]['cat_id'] = self::get_cat_child_tree($row['cat_id']);
				}
				
			}
		}
		if(isset($cat_arr))
		{
			return $cat_arr;
		}
	}

	function get_cat_child_tree($tree_id = 0)
	{
		$M_cat_list=M('audiocat_list');
		$three_arr = array();
		$sameGradeCount=$M_cat_list->where(array('parent_id'=>$tree_id))->count();
		if ($sameGradeCount || $tree_id == 0)
		{
			$child_sql = 'SELECT cat_id  AS cat_id, cat_name, parent_id ,sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'audiocat_list ' .
					"WHERE parent_id = '$tree_id' ORDER BY sort_order ASC, cat_id ASC";
			$res = $M_cat_list->query($child_sql);
			foreach ($res AS $row)
			{
			   $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
			   $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
				$three_arr[$row['cat_id']]['sort_order'] = $row['sort_order'];
			   if (isset($row['cat_id']) != NULL)
				{
					   $three_arr[$row['cat_id']]['cat_id'] = get_article_child_tree($row['cat_id']);
				}
			}
		}
		return $three_arr;
	}
}

?>
