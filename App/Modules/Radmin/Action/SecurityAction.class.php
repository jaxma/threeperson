<?php

/**
 * 	topos经销商管理系统——防伪管理
 */
class SecurityAction extends CommonAction {

    //防伪记录
    public function index() {
        import('ORG.Util.Page');
        $search = trim(I('search'));
        $inpage = trim(I('inpage'));
        
        $page_num=20;
        if (!empty($search)) {     //搜索查询记录
            $this->search = $search;
            $where['code'] = $search;
            $count_row = M('Record')->where($where)->Distinct(true)->field('code')->select();
            $count = count($count_row);
            $page = new Page($count, $page_num);
            $limit = $page->firstRow . ',' . $page->listRows;
            $sql = "select distinct code from " . C('DB_PREFIX') . "record where code='" .
                    $search . "' limit " . $limit;
            $codes = M('Record')->query($sql);
        } else if (!empty($inpage)) {     //跳转页面
            $this->inpage = $inpage;
            $count_row = M('Record')->Distinct(true)->field('code')->select();
            $count = count($count_row);
            $page = new Page($count, $page_num);
            $limit = $page->firstRow + 15 * ($inpage - 1) . ',' . $page->listRows;
            $sql = "select distinct code from " . C('DB_PREFIX') . "record limit " . $limit;
            $codes = M('Record')->query($sql);
        } else {    //所有查询记录
            $count_row = M('Record')->Distinct(true)->field('code')->select();
            $count = count($count_row);
            $page = new Page($count, $page_num);
            $limit = $page->firstRow . ',' . $page->listRows;
            $sql = "select distinct code from " . C('DB_PREFIX') . "record order by time desc limit " . $limit;
            $codes = M('Record')->query($sql);
        }
        $records = array();
        foreach ($codes as $v) {
            $sql = "select id,code,count(id) as query_times,min(time) as first_time from " .
                    C('DB_PREFIX') . "record where code='" . $v['code'] . "'";
            $record = M('Record')->query($sql);
            //最后三次查询时间
            $sql = "select time from " . C('DB_PREFIX') . "record where code='" . $v['code'] .
                    "' order by time DESC limit 3";
            $lastThreeTime = M('Record')->query($sql);
            $record[0]['lastThreeTime'] = $lastThreeTime;

            $records[] = $record[0];
        }
        $this->records = $records;
        $this->page = $page->show();
        $this->p=I('p');
        $this->limit=$page_num;
        $this->count=$count;
        $this->display();
    }

}

?>