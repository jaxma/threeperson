<?php

/**
 * 	topos经销商后台
 */
class IndexAction extends CommonAction {

    public function _initialize()
    {
        parent::_initialize();
    }

   
    public function index() {

//         $sql = " alter table `item` add image_icon varchar(200) ";
//         M()->execute($sql);
//         $sql = " alter table `news` add image_icon varchar(200) ";
//         M()->execute($sql);
//         $sql = "CREATE TABLE IF NOT EXISTS `item_icon` (
//   `id` int(10) unsigned NOT NULL,
//   `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '1,item 2,news',
//   `iconid` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'icon表对应的id',
//   `time` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `isopen` int(10) DEFAULT '0' COMMENT '是否开启',
//   `url` text CHARACTER SET utf8 COMMENT '上传的链接',
//   `sequence` int(11) DEFAULT '0',
//   `itemid` int(10) NOT NULL COMMENT '项目id'
// ) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
//         M()->execute($sql);
//         $sql = "INSERT INTO `item_icon` (`id`, `type`, `iconid`, `time`, `isopen`, `url`, `sequence`, `itemid`) VALUES
// (1, '1', '7', '1550920996', 0, '1', 0, 23),
// (2, '1', '8', '1550920996', 0, '2', 0, 23),
// (3, '1', '9', '1550920996', 0, '3', 0, 23),
// (4, '1', '10', '1550920996', 0, '5', 0, 23),
// (5, '2', '7', '1550922549', 0, 'wb', 0, 7),
// (6, '2', '8', '1550922549', 0, 'fc', 0, 7),
// (7, '2', '9', '1550922549', 0, 't', 0, 7),
// (8, '2', '10', '1550922549', 0, 'p', 0, 7),
// (9, '1', '17', '1550927465', 0, '1', 0, 23),
// (10, '1', '16', '1550927465', 0, '&amp;description=test', 0, 23),
// (11, '1', '15', '1550927465', 0, '&amp;description=test&amp;media=http://tp.yangsi.tk/upload/photo/20190223/5c7126988e184.jpg', 0, 23),
// (12, '1', '14', '1550927465', 0, '&amp;text=test', 0, 23),
// (13, '1', '13', '1550927465', 0, '1', 0, 23),
// (14, '1', '12', '1550927465', 0, '1', 0, 23),
// (15, '1', '11', '1550927465', 0, 'subject=test&amp;body=test', 0, 23),
// (16, '2', '17', '1550975934', 0, '', 0, 7),
// (17, '2', '16', '1550975934', 0, '', 0, 7),
// (18, '2', '15', '1550975934', 0, '', 0, 7),
// (19, '2', '14', '1550975934', 0, '', 0, 7),
// (20, '2', '13', '1550975934', 0, '', 0, 7),
// (21, '2', '12', '1550975934', 0, '', 0, 7),
// (22, '2', '11', '1550975934', 0, '', 0, 7);";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `item_icon`
//   ADD PRIMARY KEY (`id`);";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `item_icon`
//   MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;";
//         M()->execute($sql);


//         $sql = "CREATE TABLE IF NOT EXISTS `icon` (
//   `id` int(10) unsigned NOT NULL,
//   `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_news` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_news_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `image` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `href` text COLLATE utf8_unicode_ci,
//   `content` text COLLATE utf8_unicode_ci,
//   `content_en` text COLLATE utf8_unicode_ci,
//   `publish_time` int(10) unsigned DEFAULT '0' COMMENT '发表时间',
//   `time` int(10) unsigned DEFAULT '0',
//   `isopen` tinyint(10) DEFAULT '0' COMMENT '是否开启',
//   `many_image` text CHARACTER SET utf8 COMMENT '多图上传',
//   `sequence` int(11) DEFAULT '0' COMMENT '优先级',
//   `cat1` int(10) NOT NULL DEFAULT '0' COMMENT '一级分类',
//   `cat2` int(10) DEFAULT '0' COMMENT '二级分类'
// ) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新闻表';";
//         M()->execute($sql);
//         $sql = "INSERT INTO `icon` (`id`, `title`, `title_news`, `title_en`, `title_news_en`, `image`, `href`, `content`, `content_en`, `publish_time`, `time`, `isopen`, `many_image`, `sequence`, `cat1`, `cat2`) VALUES
// (12, 'weibo', NULL, 'weibo', NULL, NULL, 'http://service.weibo.com/share/share.php?url=', '自动补全页面信息', NULL, 0, 1550924682, 1, NULL, 99, 0, 0),
// (13, 'facebook', NULL, 'facebook', NULL, NULL, 'https://www.facebook.com/sharer.php?u=', '无参数可带', NULL, 0, 1550924910, 1, NULL, 98, 0, 0),
// (14, 'twitter', NULL, 'twitter', NULL, NULL, 'https://twitter.com/intent/tweet?url=', '&amp;text={param}', NULL, 0, 1550925358, 1, NULL, 97, 0, 0),
// (15, 'pinterest', NULL, 'pinterest', NULL, NULL, 'http://pinterest.com/pin/create/button/?url=', '&amp;description={param}&amp;media={param}', NULL, 0, 1550925503, 1, NULL, 96, 0, 0),
// (16, 'tumblr', NULL, 'tumblr', NULL, NULL, 'https://www.tumblr.com/share/link?url=', '&amp;description={param}', NULL, 0, 1550926027, 1, NULL, 95, 0, 0),
// (17, 'google', NULL, 'google', NULL, NULL, 'https://plus.google.com/share?url=', '无参数可带', NULL, 0, 1550926088, 1, NULL, 94, 0, 0),
// (18, 'linkedin', NULL, 'linkedin', NULL, NULL, 'https://www.linkedin.com/sharing/share-offsite/?url=', '无参数可带', NULL, 0, 1550976636, 1, NULL, 93, 0, 0),
// (11, 'envelope', NULL, '邮箱', NULL, NULL, 'mailto:?subject=new project&amp;body=I think you might enjoy this project by TOPOS Landscape Architects', 'mailto:? subject={new project}&amp;body={I think you might enjoy this project by TOPOS Landscape Architects}', NULL, 0, 1550924312, 1, NULL, 100, 0, 0);";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `icon`
//   ADD PRIMARY KEY (`id`),
//   ADD KEY `isopen` (`isopen`),
//   ADD KEY `sequence` (`sequence`),
//   ADD KEY `title` (`title`),
//   ADD KEY `title_en` (`title_en`),
//   ADD KEY `title_news_en` (`title_news_en`) USING BTREE,
//   ADD KEY `title_news` (`title_news`) USING BTREE;";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `icon`
//   MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;";
//         M()->execute($sql);

//         $sql = "CREATE TABLE IF NOT EXISTS `foot_icon` (
//   `id` int(10) unsigned NOT NULL,
//   `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_news` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `title_news_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `image` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
//   `href` text COLLATE utf8_unicode_ci,
//   `content` text COLLATE utf8_unicode_ci,
//   `content_en` text COLLATE utf8_unicode_ci,
//   `publish_time` int(10) unsigned DEFAULT '0' COMMENT '发表时间',
//   `time` int(10) unsigned DEFAULT '0',
//   `isopen` tinyint(10) DEFAULT '0' COMMENT '是否开启',
//   `many_image` text CHARACTER SET utf8 COMMENT '多图上传',
//   `sequence` int(11) DEFAULT '0' COMMENT '优先级',
//   `cat1` int(10) NOT NULL DEFAULT '0' COMMENT '一级分类',
//   `cat2` int(10) DEFAULT '0' COMMENT '二级分类'
// ) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新闻表';";
//         M()->execute($sql);
//         $sql = "INSERT INTO `foot_icon` (`id`, `title`, `title_news`, `title_en`, `title_news_en`, `image`, `href`, `content`, `content_en`, `publish_time`, `time`, `isopen`, `many_image`, `sequence`, `cat1`, `cat2`) VALUES
// (3, 'weibo', NULL, 'weibo', NULL, NULL, 'https://weibo.com/p/1006066329623101/home?from=page_100606&amp;mod=TAB#place', NULL, NULL, 0, 1550922984, 1, NULL, 100, 0, 0),
// (4, 'instagram', NULL, 'instagram', NULL, NULL, 'https://www.instagram.com/topos_163/', NULL, NULL, 0, 1550923002, 1, NULL, 99, 0, 0),
// (5, 'facebook', NULL, 'facebook', NULL, NULL, 'https://www.facebook.com/damin.pang.5', NULL, NULL, 0, 1550923020, 1, NULL, 98, 0, 0),
// (6, 'twitter', NULL, 'twitter', NULL, NULL, 'https://twitter.com/TOPOS13757631', NULL, NULL, 0, 1550923035, 1, NULL, 97, 0, 0),
// (7, 'pinterest', NULL, 'pinterest', NULL, NULL, 'https://www.pinterest.com/toposshenzhen/', NULL, NULL, 0, 1550923053, 1, NULL, 96, 0, 0),
// (8, 'linkedin', NULL, 'linkedin', NULL, NULL, 'https://www.linkedin.com/company/深圳拓柏景观规划设计有限公司/', NULL, NULL, 0, 1550977838, 1, NULL, 95, 0, 0);";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `foot_icon`
//   ADD PRIMARY KEY (`id`),
//   ADD KEY `isopen` (`isopen`),
//   ADD KEY `sequence` (`sequence`),
//   ADD KEY `title` (`title`),
//   ADD KEY `title_en` (`title_en`),
//   ADD KEY `title_news_en` (`title_news_en`) USING BTREE,
//   ADD KEY `title_news` (`title_news`) USING BTREE;";
//         M()->execute($sql);
//         $sql = "ALTER TABLE `foot_icon`
//   MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;";
//         M()->execute($sql);
        
        $lang_change = $this->lang==1?0:1;
        $this->lang_url = U('Index/index',array('lang'=>$lang_change));
        $project = $this->project_model->where('isopen = 1 and classical = 1')->order('sequence desc')->limit(100)->select();
        foreach ($project as $k => $v) {
        	$detail = $this->detail_arr($v['detail']);
        	$detail_en = $this->detail_arr($v['detail_en']);
        	$project[$k]['position'] = $detail[0];
        	$project[$k]['position_en'] = $detail_en[0];
        }
        $this->project = $project;
        //封面图
        $this->list = M('overpicture')->where('isopen = 1')->order('sequence desc')->limit(100)->select();
        $this->display();
    }
}
?>