<?php

class MusicModel extends Model {

	/**
      +----------------------------------------------------------
     * 歌曲列表
      +----------------------------------------------------------
     */
    public function listMusic($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Music = M("Music");
		$where = '';
		if (!empty($filter['keywords']))
		{
			$where = " AND m.title LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND m.cat_id=".$filter['cat_id'];
		}
		if ($filter['singer_id'])
		{
			$where .= " AND m.singer_id=".$filter['singer_id'];
		}

		
		
		/* 获取文章数据 */
		$sql = 'SELECT m.music_id, m.cat_id, m.title, m.sort_order,m.add_time ,m.is_open, mc.attr_name, ms.singer_name '.
			   'FROM ' . C('DB_PREFIX') . 'music AS m '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'musicattr_list AS mc ON mc.attr_id = m.cat_id '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'musicsinger_list AS ms ON ms.singer_id = m.singer_id '.
			   'WHERE 1 ' .$where.$ext. ' ORDER by '.$filter['sort_by'].' '.$filter['order_by'].
			   ' LIMIT '.$firstRow.','.$listRows;
		$result = $M_Music->query($sql);

		
		foreach($result as $key => $value){
			$result[$key]['title'] = String::msubstr($result[$key]['title'], 0, 12);
		}
        return $result;
    }
	/**
      +----------------------------------------------------------
     * 歌曲总数
      +----------------------------------------------------------
     */
    public function listMusicCount($filter = array()) {
		$M_Music = M("music");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND m.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND m.cat_id=".$filter['cat_id'];
		}
		if ($filter['singer_id'])
		{
			$where .= " AND m.singer_id=".$filter['singer_id'];
		}
        $sql = 'SELECT COUNT(music_id) AS count FROM ' . C('DB_PREFIX') . 'music AS m '.
               'WHERE 1 ' .$where;
		
        $count = $M_Music->query($sql);

		return $count[0]['count'];
    }	
	
	
	/**
      +----------------------------------------------------------
     * 相关歌曲列表
      +----------------------------------------------------------
     */
    public function listRelativeMusic($firstRow = 0, $listRows = 20 , $filter = array()) {

		$M_Music = M("Music");
		$where = '';
		$music_ids=array();
		if (!empty($filter['relative_id']))
		{
			$relativeInfo = $M_Music->where(array('music_id'=>$filter['relative_id']))->field('music_id,title,cat_id')->find(); 
			if($relativeInfo){
				$relative_songs=$M_Music->where(array('cat_id'=>$filter['cat_id'],'is_open'=>1))->field('music_id,title,cat_id')->select(); 
				foreach($relative_songs as $key=>$value){
					$music_ids[$key]=$value['music_id'];
				}
				$where .= ' AND ' . db_create_in($music_ids, 'm.music_id');
			}
			
		}
		if($_SESSION['songs'] && is_array($_SESSION['songs'])){
			$where .= ' AND ' . db_create_not_in($_SESSION['songs'], 'm.music_id');
		}

		
		/* 获取文章数据 */
		$sql = 'SELECT m.music_id, m.cat_id, m.title, m.sort_order,m.add_time ,m.is_open, mc.attr_name, ms.singer_name '.
			   'FROM ' . C('DB_PREFIX') . 'music AS m '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'musicattr_list AS mc ON mc.attr_id = m.cat_id '.
			   'LEFT JOIN ' . C('DB_PREFIX') . 'musicsinger_list AS ms ON ms.singer_id = m.singer_id '.
			   ' JOIN (SELECT ROUND(RAND() * ((SELECT MAX(music_id) FROM `' . C('DB_PREFIX') . 'music`)-(SELECT MIN(music_id) FROM `' . C('DB_PREFIX') . 'music`))+(SELECT MIN(music_id) FROM `' . C('DB_PREFIX') . 'music`)) AS music_id) AS t2 WHERE m.music_id >= t2.music_id '.
			   ' ' .$where.$ext. ' ORDER by '.$filter['sort_by'].' '.$filter['order_by'].
			   ' LIMIT '.$firstRow.','.$listRows;
		$result = $M_Music->query($sql);

		
		foreach($result as $key => $value){
			$result[$key]['title'] = String::msubstr($result[$key]['title'], 0, 12);
		}
        return $result;
    }
	/**
      +----------------------------------------------------------
     * 相关歌曲总数
      +----------------------------------------------------------
     */
    public function listRelativeMusicCount($filter = array()) {
		$M_Music = M("music");
		$where = '';
		if (!empty($filter['keyword']))
		{
			$where = " AND m.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		}
		if ($filter['cat_id'])
		{
			$where .= " AND m.cat_id=".$filter['cat_id'];
		}
		if ($filter['singer_id'])
		{
			$where .= " AND m.singer_id=".$filter['singer_id'];
		}
        $sql = 'SELECT COUNT(music_id) AS count FROM ' . C('DB_PREFIX') . 'music AS m '.
               'WHERE 1 ' .$where;
		
        $count = $M_Music->query($sql);

		return $count[0]['count'];
    }	
	
	
	
	
	/**
      +----------------------------------------------------------
     * 歌手列表
      +----------------------------------------------------------
     */
    public function listSinger($firstRow = 0, $listRows = 20 , $filter = array(), $is_hot = 0) {

		$M_Singer = M("musicsinger_list");
		$where = '';
		if (!empty($filter['keywords']))
		{
			$where = " AND s.singer_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
		}
		if ($filter['singer_type'])
		{
			$where .= " AND s.singer_type=".$filter['singer_type'];
		}
		if ($filter['initials'])
		{
			$where .= " AND s.first_word='".$filter['initials']."'";
		}
		
		if ($is_hot == 1)
		{
			$where .= " AND s.is_hot=".$is_hot;
		}
		
		
		
		/* 获取文章数据 */
		$sql = 'SELECT s.singer_id, s.singer_name, s.is_hot, s.singer_type, s.thumb_img '.
			   'FROM ' . C('DB_PREFIX') . 'musicsinger_list AS s '.
			   'WHERE 1 ' .$where.$ext. ' ORDER by '.$filter['sort_by'].' '.$filter['order_by'].
			   ' LIMIT '.$firstRow.','.$listRows;
		$result = $M_Singer->query($sql);

		
        return $result;
    }
	/**
      +----------------------------------------------------------
     * 歌手总数
      +----------------------------------------------------------
     */
    public function listSingerCount($filter = array()) {
		$M_Singer = M("musicsinger_list");
		$where = '';
		if (!empty($filter['keywords']))
		{
			$where = " AND s.singer_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
		}
		if ($filter['singer_type'])
		{
			$where .= " AND s.singer_type=".$filter['singer_type'];
		}
        $sql = 'SELECT COUNT(music_id) AS count FROM ' . C('DB_PREFIX') . 'musicsinger_list AS s '.
               'WHERE 1 ' .$where;
		
        $count = $M_Singer->query($sql);

		return $count[0]['count'];
    }
}

?>
