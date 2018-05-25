<?php
/**
 * 实时数据控制器，不是营业简报！营业简报已移至首页
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class StatmentsimpController extends AdminbaseController {

    public function index()
    {

        $date = I('date');
        if (!empty($date)) {
            $sdate = strtotime($date);
            $edate = strtotime($date . ' 23:59:59');
        } else {
            $sdate = strtotime(date('Y-m-d'));
            $edate = strtotime(date('Y-m-d 23:59:59'));
        }

        $arr = range(0, 1440);

        // 用户实时在线情况
//      $listOnline = M('user_online')->where('ctime >= '.$sdate.' and ctime <= '.$edate)->field('FROM_UNIXTIME(ctime,"%H") as hour, count(distinct user_id) as total')->group('hour')->select();
        $listOnline = M('user_online')->where('ctime >= ' . $sdate . ' and ctime <= ' . $edate)->field('FROM_UNIXTIME(ctime,"%H")*60+FROM_UNIXTIME(ctime,"%i") as m, count(*) as total')->group('m')->select();
        $gArrOnline = array();
        foreach ($listOnline as $k => $v) {
            $gArrOnline[$v['m']] = $v['total'];
        }
        unset($listOnline);
        foreach ($arr as $v) {
            if($v < 10) $v='0'.$v;
            if (empty($gArrOnline[$v])) {
                $gArrOnline[$v] = 0;
            }
        }
        ksort($gArrOnline);

        // 夹娃娃次数实时情况
        //$listNums = M('game_history')->where('ctime >= ' . $sdate . ' and ctime <= ' . $edate)->field('round(ctime/(5 * 60)) as m, count(*) as total')->group('m')->select();
        $listNums = M('game_history')->where('ctime >= ' . $sdate . ' and ctime <= ' . $edate)->field('FROM_UNIXTIME(ctime,"%H")*60+FROM_UNIXTIME(ctime,"%i") as m , count(*) as total')->group('m')->select();
        $gArrNums = array();
        foreach ($listNums as $k => $v) {
            $gArrNums[$v['m']] = $v['total'];
        }
        unset($listNums);
        foreach ($arr as $v) {
            if ($v < 10) $v = '0' . $v;
            if (empty($gArrNums[$v])) {
                $gArrNums[$v] = 0;
            }
        }
        ksort($gArrNums);


        // 夹中娃娃次数实时情况
        $listgNums = M('game_history')->where('ctime >= ' . $sdate . ' and ctime <= ' . $edate . ' and success > 0')->field('FROM_UNIXTIME(ctime,"%H")*60+FROM_UNIXTIME(ctime,"%i") as m, count(*) as total')->group('m')->select();
        $gArrgNums = array();
        foreach ($listgNums as $k => $v) {
            $gArrgNums[$v['m']] = $v['total'];
        }
        unset($listgNums);
        foreach ($arr as $v) {
            if ($v < 10) $v = '0' . $v;
            if (empty($gArrgNums[$v])) {
                $gArrgNums[$v] = 0;
            }
        }
        ksort($gArrgNums);

        $this->assign('gArrOnline', $gArrOnline);
        $this->assign('gArrNums', $gArrNums);
        $this->assign('gArrgNums', $gArrgNums);
        $this->display();
    }

}