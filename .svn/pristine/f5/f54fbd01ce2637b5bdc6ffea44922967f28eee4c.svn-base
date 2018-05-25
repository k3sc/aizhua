<?php
/**
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/12
 * Time: 11:09
 */

namespace Api\Controller;

class WaybillController extends BaseController
{

    private $statusArr = [1=>'待邮寄',2=>'待发货',3=>'已确认'];

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name)
        {
            case 'waybill_list':
                $this->waybill_list();
                break;
            case 'confirm':
                $this->confirm();
                break;
            default:
                $this->_return(404,'接口不存在');
                break;
        }

    }

    /**
     * 确认收货
     */
    private function confirm()
    {
        $waybillno = I('waybillno');
        if( empty($waybillno) )$this->_return(-1,'运单号不能为空');
        //修改运单状态为已完成
        M('waybill')->where(['user_id'=>$this->user_id,'waybillno'=>$waybillno,'status'=>2])->save(['status'=>3,'shtime'=>time()]);
        /* 通过运单号获取我的娃娃表id，我的礼品表id，用于修改娃娃或者礼品的状态 */
        $res = M('waybill')->where(['waybillno'=>$waybillno])->select();
        foreach ($res as $k => $v) {
            if( $v['user_wawas_id'] ){
                M('user_wawas')->where(['id'=>$v['user_wawas_id']])->save(['status'=>5]);
            }
            if( $v['user_gift_id'] ){
                M('users_gift')->where(['id'=>$v['user_gift_id']])->save(['type'=>4]);
            }
        }
        $this->_return(1,'确认收货成功');
    }


    /**
     * 运单列表
     */
    private function waybill_list()
    {
        $page = I('page',1);
        $pagesize = I('pagesize',20);
        //获取用户的所有运单
        $userWaybillAll = M('waybill as a')
            ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
            ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
            ->join('left join cmf_user_addr as d on a.addr_id = d.addr_id')
            ->field('a.*,b.wawa_id,c.gift_id,d.username as uname,d.mobile as phone,d.addr,d.addr_info')
            ->where(['a.user_id'=>$this->user_id])
            ->order('a.ctime desc')
            ->page($page,$pagesize)
            ->select();
        //超出14天未确认收货，则自动确认收货
        foreach ($userWaybillAll as $k => $v) {
            if( $v['status'] == 2 ){
                if( time() - $v['fhtime'] > 14*24*60*60 )
                    M('waybill')->where(['waybillno'=>$v['waybillno']])->save(['status'=>3,'shtime'=>time()]);
            }
        }


        foreach ($userWaybillAll as $k => $v) {
            if( $v['wawa_id'] ){
                $wawaname = M('gift')->where(['id'=>$v['wawa_id']])->getField('giftname');
                $userWaybillAll[$k]['wawaname'] = $wawaname;
            }else{
                $userWaybillAll[$k]['wawaname'] = '';
            }
            if( $v['gift_id'] ){
                $giftname = M('give_gift')->where(['id'=>$v['gift_id']])->getField('name');
                $userWaybillAll[$k]['giftname'] = $giftname;
            }else{
                $userWaybillAll[$k]['giftname'] = '';
            }
        }

        $arr = [];
        foreach($userWaybillAll as $k=>$v){
            if(!isset($arr[$v['waybillno']])){
                $arr[$v['waybillno']]=array(
                    'waybill_id'=>$v['waybill_id'],
                    'waybillno' =>$v['waybillno'],
                    'status' => $v['status'],
                    'remark' => $v['remark'],
                    'ctime' => $v['ctime'],//运单生成时间
                    'fhtime' => $v['fhtime'],//发货时间
                    'shtime' => $v['shtime'],//收货时间
                    'uname' => $v['uname'],
                    'phone' => $v['phone'],
                    'addr' => $v['addr'],
                    'addr_info' => $v['addr_info'],
                    'kdname' => $v['kdname'],
                    'kdno' => $v['kdno'],
                );

                if(!empty($v['wawa_id'])) {
                    $flag=1;
                    foreach($arr[$v['waybillno']]['goods'] as $kk => $vv){
                        if($vv['name']==$v['wawaname']){
                            $flag=0;
//                            $vv['num']+=1;
                            $arr[$v['waybillno']]['goods'][$kk]['num']+=1;
                        }
                    }
                    if ($flag) {
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1);
                    }
                }else{
                    $mark = 1;
                    foreach($arr[$v['waybillno']]['goods'] as $kkk => $vvv){
                        if($vvv['name']==$v['giftname']){
                            $mark=0;
//                            $vvv['num']+=1;
                            $arr[$v['waybillno']]['goods'][$kkk]['num']+=1;
                        }
                    }
                    if ($mark) {
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1);
                    }
                }
            }else{
                if(!empty($v['wawa_id'])) {
                    $flag=0;
                    foreach($arr[$v['waybillno']]['goods'] as $kk=>$vv){
                        if($vv['name']==$v['wawaname']){
                            $flag=1;
                            $arr[$v['waybillno']]['goods'][$kk]['num']+=1;
                        }
                    }
                    if (!$flag) {
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1);
                    }
                }else{
                    $mark = 1;
                    foreach($arr[$v['waybillno']]['goods'] as $kkk => $vvv){
                        if($vvv['name']==$v['giftname']){
                            $mark=0;
//                            $vvv['num']+=1;
                            $arr[$v['waybillno']]['goods'][$kkk]['num']+=1;
                        }
                    }
                    if ($mark) {
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1);
                    }
                }
            }
        }
        $arr = array_values($arr);


        $this->_return(1,'获取成功',$arr);
    }

}