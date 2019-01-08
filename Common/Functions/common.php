<?php
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
	function article_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
	{
		static $res = NULL;

		if ($res === NULL)
		{
			$sql = "SELECT c.*, COUNT(s.cat_id) AS has_children, COUNT(a.article_id) AS aricle_num ".
			   " FROM " . C('DB_PREFIX') . "articlecat AS c".
			   " LEFT JOIN " . C('DB_PREFIX') . "articlecat AS s ON s.parent_id=c.cat_id".
			   " LEFT JOIN " . C('DB_PREFIX') . "article AS a ON a.cat_id=c.cat_id".
			   " GROUP BY c.cat_id ".
			   " ORDER BY parent_id, sort_order ASC";
			$model = M("articlecat");
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
				$select .= ' cat_type="' . $var['cat_type'] . '" ';
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
	 * 过滤和排序所有文章分类，返回一个带有缩进级别的数组
	 *
	 * @access  private
	 * @param   int     $cat_id     上级分类ID
	 * @param   array   $arr        含有所有分类的数组
	 * @param   int     $level      级别
	 * @return  void
	 */
	function article_cat_options($spec_cat_id, $arr)
	{
		$cat_options = array();


		if (isset($cat_options[$spec_cat_id]))
		{
			return $cat_options[$spec_cat_id];
		}

		if (!isset($cat_options[0]))
		{
			$level = $last_cat_id = 0;
			$options = $cat_id_array = $level_array = array();
			
			foreach ($arr as $k=>$v)
			{
				// exit('111');
				foreach ($arr AS $key => $value)
				{
					$cat_id = $value['cat_id'];
					if ($level == 0 && $last_cat_id == 0)
					{
						if ($value['parent_id'] > 0)
						{
							break;
						}

						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);

						if ($value['has_children'] == 0)
						{
							continue;
						}
						$last_cat_id  = $cat_id;
						$cat_id_array = array($cat_id);
						$level_array[$last_cat_id] = ++$level;
						continue;
					}

					if ($value['parent_id'] == $last_cat_id)
					{
						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);

						if ($value['has_children'] > 0)
						{
							if (end($cat_id_array) != $last_cat_id)
							{
								$cat_id_array[] = $last_cat_id;
							}
							$last_cat_id    = $cat_id;
							$cat_id_array[] = $cat_id;
							$level_array[$last_cat_id] = ++$level;
						}
					}
					elseif ($value['parent_id'] > $last_cat_id)
					{
						break;
					}
				}

				$count = count($cat_id_array);
				if ($count > 1)
				{
					$last_cat_id = array_pop($cat_id_array);
				}
				elseif ($count == 1)
				{
					if ($last_cat_id != end($cat_id_array))
					{
						$last_cat_id = end($cat_id_array);
					}
					else
					{
						$level = 0;
						$last_cat_id = 0;
						$cat_id_array = array();
						continue;
					}
				}

				if ($last_cat_id && isset($level_array[$last_cat_id]))
				{
					$level = $level_array[$last_cat_id];
				}
				else
				{
					$level = 0;
				}
			}
			$cat_options[0] = $options;
		}
		else
		{
			$options = $cat_options[0];
		}

		if (!$spec_cat_id)
		{
			return $options;
		}
		else
		{
			if (empty($options[$spec_cat_id]))
			{
				return array();
			}

			$spec_cat_id_level = $options[$spec_cat_id]['level'];

			foreach ($options AS $key => $value)
			{
				if ($key != $spec_cat_id)
				{
					unset($options[$key]);
				}
				else
				{
					break;
				}
			}

			$spec_cat_id_array = array();
			foreach ($options AS $key => $value)
			{
				if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
					($spec_cat_id_level > $value['level']))
				{
					break;
				}
				else
				{
					$spec_cat_id_array[$key] = $value;
				}
			}
			$cat_options[$spec_cat_id] = $spec_cat_id_array;

			return $spec_cat_id_array;
		}
	}
	
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function article_categories_tree($cat_id = 0)
	{
		$articleCat=M('articlecat');
		if ($cat_id > 0)
		{
			$parentCat=$articleCat->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
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
		$sameGradeCount=$articleCat->where(array('parent_id'=>$parent_id))->count();
		if ($sameGradeCount)
		{
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT a.cat_id, a.cat_name, a.sort_order AS parent_order, a.cat_id, ' .
						'b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order AS child_order ' .
					'FROM ' . C('DB_PREFIX') . 'articlecat AS a ' .
					'LEFT JOIN ' . C('DB_PREFIX') . 'articlecat AS b ON b.parent_id = a.cat_id ' .
					"WHERE a.parent_id = '$parent_id' AND a.cat_type=1 ORDER BY parent_order ASC, a.cat_id ASC, child_order ASC";
		}
		else
		{
			/* 获取当前分类及其父分类 */
			$sql = 'SELECT a.cat_id, a.cat_name, b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'articlecat AS a ' .
					'LEFT JOIN ' . C('DB_PREFIX') . 'articlecat AS b ON b.parent_id = a.cat_id ' .
					"WHERE b.parent_id = '$parent_id' AND b.cat_type = 1 ORDER BY sort_order ASC";
		}
		
		
		$res=$articleCat->query($sql);

		$cat_arr = array();
		foreach ($res AS $row)
		{
			$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
			$cat_arr[$row['cat_id']]['name'] = $row['cat_name'];

			if ($row['child_id'] != NULL)
			{
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['id']   = $row['child_id'];
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['name'] = $row['child_name'];
			}
		}

		return $cat_arr;
	}

	/**
	 * 获得指定文章分类的所有上级分类
	 *
	 * @access  public
	 * @param   integer $cat    分类编号
	 * @return  array
	 */
	function get_article_parent_cats($cat)
	{
		$articleCat=M('articlecat');
		
		if ($cat == 0)
		{
			return array();
		}
		
		$arr = $articleCat->field('cat_id,cat_name,parent_id')->select();

		if (empty($arr))
		{
			return array();
		}

		$index = 0;
		$cats  = array();

		while (1)
		{
			foreach ($arr AS $row)
			{
				if ($cat == $row['cat_id'])
				{
					$cat = $row['parent_id'];

					$cats[$index]['cat_id']   = $row['cat_id'];
					$cats[$index]['cat_name'] = $row['cat_name'];

					$index++;
					break;
				}
			}

			if ($index == 0 || $cat == 0)
			{
				break;
			}
		}

		return $cats;
	}
	
	
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function get_article_categories_tree($cat_id = 0)
	{
		$articleCat=M('articlecat');
		if ($cat_id > 0)
		{
			$parentCat=$articleCat->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
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
		$sameGradeCount=$articleCat->where(array('parent_id'=>$parent_id))->count();
		
		
		if ($sameGradeCount || $parent_id == 0)
		{
			
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT cat_id,cat_name ,parent_id ' .
					'FROM ' . C('DB_PREFIX') . 'articlecat ' .
					"WHERE parent_id = '$parent_id' ORDER BY sort_order ASC, cat_id ASC";

			$res = $articleCat->query($sql);

			foreach ($res AS $row)
			{
				
				$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
				$cat_arr[$row['cat_id']]['name'] = $row['cat_name'];

				if (isset($row['cat_id']) != NULL)
				{
					$cat_arr[$row['cat_id']]['cat_id'] = get_article_child_tree($row['cat_id']);
				}
				
			}
		}
		if(isset($cat_arr))
		{
			return $cat_arr;
		}
	}

	function get_article_child_tree($tree_id = 0)
	{
		$articleCat=M('articlecat');
		$three_arr = array();
		$sameGradeCount=$articleCat->where(array('parent_id'=>$tree_id))->count();
		if ($sameGradeCount || $tree_id == 0)
		{
			$child_sql = 'SELECT cat_id, cat_name, parent_id ' .
					'FROM ' . C('DB_PREFIX') . 'articlecat ' .
					"WHERE parent_id = '$tree_id' ORDER BY sort_order ASC, cat_id ASC";
			$res = $articleCat->query($child_sql);
			foreach ($res AS $row)
			{
			   $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
			   $three_arr[$row['cat_id']]['name'] = $row['cat_name'];

			   if (isset($row['cat_id']) != NULL)
				{
					   $three_arr[$row['cat_id']]['cat_id'] = get_article_child_tree($row['cat_id']);
				}
			}
		}
		return $three_arr;
	}
	
	/**
	 * 获得指定文章分类下所有底层分类的ID
	 *
	 * @access  public
	 * @param   integer     $cat        指定的分类ID
	 *
	 * @return void
	 */
	function get_article_children ($cat = 0)
	{
		return db_create_in(array_unique(array_merge(array($cat), array_keys(article_cat_list($cat, 0, false)))), 'cat_id');
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
	function goods_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
	{
		static $res = NULL;

		if ($res === NULL)
		{
			$sql = "SELECT c.*, COUNT(s.cat_id) AS has_children, COUNT(a.goods_id) AS aricle_num ".
			   " FROM " . C('DB_PREFIX') . "goodscat AS c".
			   " LEFT JOIN " . C('DB_PREFIX') . "goodscat AS s ON s.parent_id=c.cat_id".
			   " LEFT JOIN " . C('DB_PREFIX') . "goods AS a ON a.cat_id=c.cat_id".
			   " GROUP BY c.cat_id ".
			   " ORDER BY parent_id, sort_order ASC";

			$model = M("goodscat");
			$res = $model->query($sql);
		}
		if (empty($res) == true)
		{
			return $re_type ? '' : array();
		}
		
		

		$options = goods_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组

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
				$select .= ' cat_type="' . $var['cat_type'] . '" ';
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
	 * 过滤和排序所有商品分类，返回一个带有缩进级别的数组
	 *
	 * @access  private
	 * @param   int     $cat_id     上级分类ID
	 * @param   array   $arr        含有所有分类的数组
	 * @param   int     $level      级别
	 * @return  void
	 */
	function goods_cat_options($spec_cat_id, $arr)
	{
		$cat_options = array();


		if (isset($cat_options[$spec_cat_id]))
		{
			return $cat_options[$spec_cat_id];
		}

		if (!isset($cat_options[0]))
		{
			$level = $last_cat_id = 0;
			$options = $cat_id_array = $level_array = array();
			
			foreach ($arr as $k=>$v)
			{
				// exit('111');
				foreach ($arr AS $key => $value)
				{
					$cat_id = $value['cat_id'];
					if ($level == 0 && $last_cat_id == 0)
					{
						if ($value['parent_id'] > 0)
						{
							break;
						}

						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);

						if ($value['has_children'] == 0)
						{
							continue;
						}
						$last_cat_id  = $cat_id;
						$cat_id_array = array($cat_id);
						$level_array[$last_cat_id] = ++$level;
						continue;
					}

					if ($value['parent_id'] == $last_cat_id)
					{
						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);

						if ($value['has_children'] > 0)
						{
							if (end($cat_id_array) != $last_cat_id)
							{
								$cat_id_array[] = $last_cat_id;
							}
							$last_cat_id    = $cat_id;
							$cat_id_array[] = $cat_id;
							$level_array[$last_cat_id] = ++$level;
						}
					}
					elseif ($value['parent_id'] > $last_cat_id)
					{
						break;
					}
				}

				$count = count($cat_id_array);
				if ($count > 1)
				{
					$last_cat_id = array_pop($cat_id_array);
				}
				elseif ($count == 1)
				{
					if ($last_cat_id != end($cat_id_array))
					{
						$last_cat_id = end($cat_id_array);
					}
					else
					{
						$level = 0;
						$last_cat_id = 0;
						$cat_id_array = array();
						continue;
					}
				}

				if ($last_cat_id && isset($level_array[$last_cat_id]))
				{
					$level = $level_array[$last_cat_id];
				}
				else
				{
					$level = 0;
				}
			}
			$cat_options[0] = $options;
		}
		else
		{
			$options = $cat_options[0];
		}

		if (!$spec_cat_id)
		{
			return $options;
		}
		else
		{
			if (empty($options[$spec_cat_id]))
			{
				return array();
			}

			$spec_cat_id_level = $options[$spec_cat_id]['level'];

			foreach ($options AS $key => $value)
			{
				if ($key != $spec_cat_id)
				{
					unset($options[$key]);
				}
				else
				{
					break;
				}
			}

			$spec_cat_id_array = array();
			foreach ($options AS $key => $value)
			{
				if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
					($spec_cat_id_level > $value['level']))
				{
					break;
				}
				else
				{
					$spec_cat_id_array[$key] = $value;
				}
			}
			$cat_options[$spec_cat_id] = $spec_cat_id_array;

			return $spec_cat_id_array;
		}
	}
	
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function goods_categories_tree($cat_id = 0)
	{
		$goodsCat=M('goodscat');
		if ($cat_id > 0)
		{
			$parentCat=$goodsCat->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
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
		$sameGradeCount=$goodsCat->where(array('parent_id'=>$parent_id))->count();
		if ($sameGradeCount)
		{
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT a.cat_id, a.cat_name, a.sort_order AS parent_order, a.cat_id, ' .
						'b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order AS child_order ' .
					'FROM ' . C('DB_PREFIX') . 'goodscat AS a ' .
					'LEFT JOIN ' . C('DB_PREFIX') . 'goodscat AS b ON b.parent_id = a.cat_id ' .
					"WHERE a.parent_id = '$parent_id' AND a.cat_type=1 ORDER BY parent_order ASC, a.cat_id ASC, child_order ASC";
		}
		else
		{
			/* 获取当前分类及其父分类 */
			$sql = 'SELECT a.cat_id, a.cat_name, b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order ' .
					'FROM ' . C('DB_PREFIX') . 'goodscat AS a ' .
					'LEFT JOIN ' . C('DB_PREFIX') . 'goodscat AS b ON b.parent_id = a.cat_id ' .
					"WHERE b.parent_id = '$parent_id' AND b.cat_type = 1 ORDER BY sort_order ASC";
		}
		
		
		$res=$goodsCat->query($sql);

		$cat_arr = array();
		foreach ($res AS $row)
		{
			$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
			$cat_arr[$row['cat_id']]['name'] = $row['cat_name'];

			if ($row['child_id'] != NULL)
			{
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['id']   = $row['child_id'];
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['name'] = $row['child_name'];
			}
		}

		return $cat_arr;
	}

	/**
	 * 获得指定商品分类的所有上级分类
	 *
	 * @access  public
	 * @param   integer $cat    分类编号
	 * @return  array
	 */
	function get_goods_parent_cats($cat)
	{
		$goodsCat=M('goodscat');
		
		if ($cat == 0)
		{
			return array();
		}
		
		$arr = $goodsCat->field('cat_id,cat_name,parent_id')->select();

		if (empty($arr))
		{
			return array();
		}

		$index = 0;
		$cats  = array();

		while (1)
		{
			foreach ($arr AS $row)
			{
				if ($cat == $row['cat_id'])
				{
					$cat = $row['parent_id'];

					$cats[$index]['cat_id']   = $row['cat_id'];
					$cats[$index]['cat_name'] = $row['cat_name'];

					$index++;
					break;
				}
			}

			if ($index == 0 || $cat == 0)
			{
				break;
			}
		}

		return $cats;
	}
	
	
	/**
	 * 获得指定分类同级的所有分类以及该分类下的子分类
	 *
	 * @access  public
	 * @param   integer     $cat_id     分类编号
	 * @return  array
	 */
	function get_goods_categories_tree($cat_id = 0)
	{
		$goodsCat=M('goodscat');
		if ($cat_id > 0)
		{
			$parentCat=$goodsCat->where(array('cat_id'=>$cat_id))->field('parent_id')->find(); 
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
		$sameGradeCount=$goodsCat->where(array('parent_id'=>$parent_id))->count();
		
		
		if ($sameGradeCount || $parent_id == 0)
		{
			
			/* 获取当前分类及其子分类 */
			$sql = 'SELECT cat_id,cat_name ,parent_id ' .
					'FROM ' . C('DB_PREFIX') . 'goodscat ' .
					"WHERE parent_id = '$parent_id' ORDER BY sort_order ASC, cat_id ASC";

			$res = $goodsCat->query($sql);

			foreach ($res AS $row)
			{
				
				$cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
				$cat_arr[$row['cat_id']]['name'] = $row['cat_name'];

				if (isset($row['cat_id']) != NULL)
				{
					$cat_arr[$row['cat_id']]['cat_id'] = get_goods_child_tree($row['cat_id']);
				}
				
			}
		}
		if(isset($cat_arr))
		{
			return $cat_arr;
		}
	}

	function get_goods_child_tree($tree_id = 0)
	{
		$goodsCat=M('goodscat');
		$three_arr = array();
		$sameGradeCount=$goodsCat->where(array('parent_id'=>$tree_id))->count();
		if ($sameGradeCount || $tree_id == 0)
		{
			$child_sql = 'SELECT cat_id, cat_name, parent_id ' .
					'FROM ' . C('DB_PREFIX') . 'goodscat ' .
					"WHERE parent_id = '$tree_id' ORDER BY sort_order ASC, cat_id ASC";
			$res = $goodsCat->query($child_sql);
			foreach ($res AS $row)
			{
			   $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
			   $three_arr[$row['cat_id']]['name'] = $row['cat_name'];

			   if (isset($row['cat_id']) != NULL)
				{
					   $three_arr[$row['cat_id']]['cat_id'] = get_goods_child_tree($row['cat_id']);
				}
			}
		}
		return $three_arr;
	}
	
	/**
	 * 获得指定商品分类下所有底层分类的ID
	 *
	 * @access  public
	 * @param   integer     $cat        指定的分类ID
	 *
	 * @return void
	 */
	function get_goods_children ($cat = 0)
	{
		return db_create_in(array_unique(array_merge(array($cat), array_keys(goods_cat_list($cat, 0, false)))), 'cat_id');
	}
	
	
	/**
	 * 创建像这样的查询: "IN('a','b')";
	 *
	 * @access   public
	 * @param    mix      $item_list      列表数组或字符串
	 * @param    string   $field_name     字段名称
	 *
	 * @return   void
	 */
	function db_create_in($item_list, $field_name = '')
	{
		if (empty($item_list))
		{
			return $field_name . " IN ('') ";
		}
		else
		{
			if (!is_array($item_list))
			{
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list AS $item)
			{
				if ($item !== '')
				{
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp))
			{
				return $field_name . " IN ('') ";
			}
			else
			{
				return $field_name . ' IN (' . $item_list_tmp . ') ';
			}
		}
	}
	
	/**
	 * 创建像这样的查询: "NOT IN('a','b')";
	 *
	 * @access   public
	 * @param    mix      $item_list      列表数组或字符串
	 * @param    string   $field_name     字段名称
	 *
	 * @return   void
	 */
	function db_create_not_in($item_list, $field_name = '')
	{
		if (empty($item_list))
		{
			return $field_name . " NOT IN ('') ";
		}
		else
		{
			if (!is_array($item_list))
			{
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list AS $item)
			{
				if ($item !== '')
				{
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp))
			{
				return $field_name . " NOT IN ('') ";
			}
			else
			{
				return $field_name . ' NOT IN (' . $item_list_tmp . ') ';
			}
		}
	}
	
	/**
	 * 对 MYSQL LIKE 的内容进行转义
	 *
	 * @access      public
	 * @param       string      string  内容
	 * @return      string
	 */
	function mysql_like_quote($str)
	{
		return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
	}
	
	
?>
