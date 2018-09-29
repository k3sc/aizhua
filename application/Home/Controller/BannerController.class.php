<?php
/**
 * author by OCY, 2018/09/28 14:57.
 */

namespace Home\Controller;


use Common\Controller\HomebaseController;

class BannerController extends HomebaseController
{
    public function rankingList(){
        $arr = [
            'all'=>['master','deposit'],
            'week'=>['master','deposit'],
        ];
        foreach ($arr as $key=>$val){
            foreach ($val as $k=>$v){
                $result[$key][$v] = $this->getBanData($key,$v);
            }
        }
        $this->assign('alldata',$result['all']['master']);
        $this->assign('weekdata',$result['week']);
        $this->display();
    }
    public function getBanData($ban,$type){
        $where = " 1=1 and user_type=2 ";
        $order = 'uwawacount desc';
        $startWeek = strtotime('this week',strtotime(date('Y-m-d',time())));
        $endWeek = strtotime('this week+7day',strtotime(date('Y-m-d',time()))) -1;
        $ban = $ban?:'all';
        $type = $type?:'master';
        if($ban == 'week'){
            $where .= " and uwawa.ctime >= {$startWeek} and uwawa.ctime<={$endWeek} ";
            $limit = "0,20";
        }else{
            $limit = "0,10";
        }
        if($type == 'deposit'){
            $order = 'summoney desc';
        }else{
            $order = 'uwawacount desc';
        }

        $where .= " and uwawa.is_del=0 and uwawa.is_receive=0 ";
        $data = M('users as u')->field('u.id,u.user_nicename,u.avatar,count(uwawa.id) as uwawacount,ppaayy.summoney')
            ->join('left join cmf_user_wawas as uwawa on u.id = uwawa.user_id')
            ->join('LEFT JOIN ( SELECT pay.user_id, pay.money AS pay_money, sum( pay.money ) AS summoney  FROM cmf_pay_record AS pay
	                WHERE pay.`status`=1 GROUP BY pay.user_id ) as ppaayy on ppaayy.user_id = u.id')
            ->where($where)
            ->group('u.id')
            ->order($order)
            ->limit($limit)
            ->select();

        return $data;
    }
}