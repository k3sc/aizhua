<?php
/**
 * 运单管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/19
 * Time: 10:18
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class WaybillController extends AdminbaseController
{

    private $statusArr = [1=>'待邮寄',2=>'已发货',3=>'已确认'];

    public function lists()
    {
        $where = '1=1';
        $keyword = I('keyword','');
        if( $keyword ){
            $where .= " and waybillno like '%".$keyword."%' or kdno like '%".$keyword."%'";
            $this->assign('keyword',$keyword);
        }
        $status = I('status','');
        if( $status ){
            $where .= " and a.status = $status";
            $this->assign('status',$status);
        }
        $username = I('username');
        if( $username ){
            $where .= " and a.uname like '%".$username."%'";
            $this->assign('username',$username);
        }
        $mobile = I('mobile');
        if( $mobile ){
            $where .= " and a.phone like '%".$mobile."%'";
            $this->assign('mobile',$mobile);
        }
        $uid = I('uid');
        if( $uid ){
            $where .= " and a.user_id = '".$uid."'";
            $this->assign('uid',$uid);
        }


        $count = M('waybill as a')
            ->join('left join cmf_users as e on a.user_id = e.id')
            ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
            ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
            ->where($where)
            ->count('waybillno');

        $page = $this->page($count,20);
        $this->assign('page',$page->show('Admin'));

        //获取用户的所有运单
        $userWaybillAll = M('waybill as a')
            ->join('left join cmf_users as e on a.user_id = e.id')
            ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
            ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
//            ->join('left join cmf_user_addr as d on a.addr_id = d.addr_id')
            ->field('a.*,b.wawa_id,c.gift_id,e.user_nicename')
            ->where($where)
            ->limit($page->firstRow.','.$page->listRows)
            ->order('ctime desc')
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
                $userWaybillAll[$k]['wawa_id'] = $v['wawa_id'];
            }else{
                $userWaybillAll[$k]['wawaname'] = '';
            }
            if( $v['gift_id'] ){
                $giftname = M('give_gift')->where(['id'=>$v['gift_id']])->getField('name');
                $userWaybillAll[$k]['giftname'] = $giftname;
                $userWaybillAll[$k]['gift_id'] = $v['gift_id'];
            }else{
                $userWaybillAll[$k]['giftname'] = '';
            }
        }

        $arr = [];
        foreach($userWaybillAll as $k=>$v){
            if(!isset($arr[$v['waybillno']])){
                $arr[$v['waybillno']]=array(
                    'user_id'       => $v['user_id'],
                    'user_nicename' => $v['user_nicename'],
                    'waybill_id'    => $v['waybill_id'],
                    'waybillno'     => $v['waybillno'],
                    'status'        => $v['status'],
                    'remark'        => $v['remark'],
                    'ctime'         => $v['ctime'],//运单生成时间
                    'fhtime'        => $v['fhtime'],//发货时间
                    'shtime'        => $v['shtime'],//收货时间
                    'uname'         => $v['uname'],
                    'phone'         => $v['phone'],
                    'addr'          => $v['addr'],
                    'addr_info'     => $v['addr_info'],
                    'kdname'        => $v['kdname'],
                    'kdno'          => $v['kdno'],
                    'goodsname'     => $v['goodsname'],
                    'sys_remark'    => $v['sys_remark'],
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift_id'=>$v['gift_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift_id'=>$v['gift_id']);
                    }
                }
            }
	$money = M('pay_record')->where("user_id='{$v['user_id']}' and status=1")->sum('money');
            $arr[$v['waybillno']]['total_payed'] = $money?:0;
        }

        $arr = array_values($arr);


        foreach ($arr as $k => $v){
            foreach ($v['goods'] as $vv) {
                $arr[$k]['num'] += $vv['num'];
            }
        }

        $this->assign('status_name',$this->statusArr);
        $this->assign('data',$arr);

        $this->display();

    }

    /* *
     * 逾期寄存的娃娃信息
     * @author by OCY
     */
    public function overdue(){
        $day = I('day')?I('day'):30;
        if(!is_numeric($day)){
            return false;
        }
        $daytime = time() - ($day * 86400);
        /* *
         * is_receive = 0 是否获赠
         * @author by OCY
         */
        $where = ' 1=1 and b.ctime < '.$daytime.' and b.is_del=0 and b.status in (0,1) and b.is_receive=0';
        $count = M('user_wawas as b')
            ->join('left join cmf_users as e on b.user_id = e.id')
            ->where($where)
            ->count();

        $page = $this->page($count,15);
        $this->assign('page',$page->show('Admin'));

        //获取用户的所有运单
        $userWaybillAll = M('user_wawas as b')
            ->join('left join cmf_waybill as bill on bill.user_wawas_id = b.id')
            ->join('left join cmf_users as e on b.user_id = e.id')
            ->field('bill.*,b.*,b.status as wawa_status,b.ctime as getwawatime,e.user_nicename')
            ->where($where)
            ->limit($page->firstRow.','.$page->listRows)
            ->order('getwawatime desc')
            ->select();

        foreach ($userWaybillAll as $k => $v) {
            if( $v['wawa_id'] ){
                $wawaname = M('gift')->where(['id'=>$v['wawa_id']])->getField('giftname');
                $userWaybillAll[$k]['wawaname'] = $wawaname;
                $userWaybillAll[$k]['wawa_id'] = $v['wawa_id'];
            }else{
                $userWaybillAll[$k]['wawaname'] = '';
            }
            if( $v['gift_id'] ){
                $giftname = M('give_gift')->where(['id'=>$v['gift_id']])->getField('name');
                $userWaybillAll[$k]['giftname'] = $giftname;
                $userWaybillAll[$k]['gift_id'] = $v['gift_id'];
            }else{
                $userWaybillAll[$k]['giftname'] = '';
            }
            $money = M('pay_record')->where("user_id='{$v['user_id']}' and status=1")->sum('money');
            $userWaybillAll[$k]['total_payed'] = $money;
        }


        /*$arr = [];
        foreach($userWaybillAll as $k=>$v){
            if(!isset($arr[$v['waybillno']])){
                $arr[$v['waybillno']]=array(
                    'user_id'       => $v['user_id'],
                    'user_nicename' => $v['user_nicename'],
                    'waybill_id'    => $v['waybill_id'],
                    'waybillno'     => $v['waybillno'],
                    'status'        => $v['status'],
                    'remark'        => $v['remark'],
                    'ctime'         => $v['ctime'],//运单生成时间
                    'fhtime'        => $v['fhtime'],//发货时间
                    'shtime'        => $v['shtime'],//收货时间
                    'uname'         => $v['uname'],
                    'phone'         => $v['phone'],
                    'addr'          => $v['addr'],
                    'addr_info'     => $v['addr_info'],
                    'kdname'        => $v['kdname'],
                    'kdno'          => $v['kdno'],
                    'goodsname'     => $v['goodsname'],
                    'sys_remark'    => $v['sys_remark'],
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift_id'=>$v['gift_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                        $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift_id'=>$v['gift_id']);
                    }
                }
            }
            $money = M('pay_record')->where("user_id='{$v['user_id']}' and status=1")->sum('money');
            $arr[$v['waybillno']]['total_payed'] = $money?:0;
        }
        $arr = array_values($arr);


        foreach ($arr as $k => $v){
            foreach ($v['goods'] as $vv) {
                $arr[$k]['num'] += $vv['num'];
            }
        }*/

        $this->assign('data',$userWaybillAll);
        $this->display();
    }


    /**
     * 发货
     */
    public function fahuo()
    {
        $waybillno = I('waybillno');
        $this->assign('waybillno',$waybillno);
        $this->assign('kd',M('kd')->select());
        if( IS_POST ){
            $post = I('post.');
            $post['fhtime'] = time();
            $post['status'] = 2;
            if( in_array('',$post) )$this->error('各项参数不能为空');
            M('waybill')->where(['waybillno'=>$waybillno,'status'=>1])->save($post);
            if( mysqli_affected_rows() >= 0 ){
                $this->success('操作成功',U('Waybill/lists'));exit;
            }else{
                $this->error('操作失败');exit;
            }
        }
        $this->display();
    }


    public function prints(){
        $waybillno = I('waybillno');
        $where['waybillno'] = $waybillno;
        //获取用户的所有运单
        $userWaybillAll = M('waybill as a')
            ->join('left join cmf_users as e on a.user_id = e.id')
            ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
            ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
            ->join('left join cmf_user_addr as d on a.addr_id = d.addr_id')
            ->field('a.*,b.wawa_id,c.gift_id,d.username as uname,d.mobile as phone,d.addr,d.addr_info,e.user_nicename')
            ->where($where)
            ->select();

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
                    'user_id'       => $v['user_id'],
                    'user_nicename' => $v['user_nicename'],
                    'waybill_id'    => $v['waybill_id'],
                    'waybillno'     => $v['waybillno'],
                    'status'        => $v['status'],
                    'remark'        => $v['remark'],
                    'ctime'         => $v['ctime'],//运单生成时间
                    'fhtime'        => $v['fhtime'],//发货时间
                    'shtime'        => $v['shtime'],//收货时间
                    'uname'         => $v['uname'],
                    'phone'         => $v['phone'],
                    'addr'          => $v['addr'],
                    'addr_info'     => $v['addr_info'],
                    'kdname'        => $v['kdname'],
                    'kdno'          => $v['kdno'],
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
        $this->assign('status_name',$this->statusArr);
        $this->assign('vo',$arr[0]);
        $this->display();
    }



    public function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("waybill")->delete($id);
            if($result){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('数据传入失败！');
        }
    }


    public function doExport(){

        $s = strtotime(I('startTime'));
        $e = strtotime(I('endTime')." 23:59:59");
        $where = "a.ctime >= " . $s;
        $where .= " and a.ctime < " . $e;
        $where .= " and a.status = 1";

        //获取用户的所有运单
        $userWaybillAll = M('waybill as a')
            ->join('left join cmf_users as e on a.user_id = e.id')
            ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
            ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
            ->field('a.*,b.wawa_id,c.gift_id,e.user_nicename')
            ->where($where)
            ->order('a.ctime desc')
            ->select();

        if( empty($userWaybillAll) ) $this->error('所选时间没有可发货的订单','admin/waybill/lists');

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
                    'user_id'       => $v['user_id'],
                    'user_nicename' => $v['user_nicename'],
                    'waybill_id'    => $v['waybill_id'],
                    'waybillno'     => $v['waybillno'],
                    'status'        => $v['status'],
                    'remark'        => $v['remark'],
                    'ctime'         => $v['ctime'],//运单生成时间
                    'fhtime'        => $v['fhtime'],//发货时间
                    'shtime'        => $v['shtime'],//收货时间
                    'uname'         => $v['uname'],
                    'phone'         => $v['phone'],
                    'addr'          => $v['addr'],
                    'addr_info'     => $v['addr_info'],
                    'kdname'        => $v['kdname'],
                    'kdno'          => $v['kdno'],
                    'sys_remark'    => $v['sys_remark'],
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
	    $money = M('pay_record')->where("user_id='{$v['user_id']}' and status=1")->sum('money');
            $arr[$v['waybillno']]['total_payed'] = $money;
        }
        $arr = array_values($arr);

        foreach ($arr as $k =>$v) {
            $goods = '';
            foreach ($v['goods'] as $vo) {
                $goods .= $vo['name'] . ' X ' . $vo['num'] . "\r\n";
            }
        }

        $data = [];
        foreach ($arr as $k => $v) {
            $data[$k]['waybillno'] = $v['waybillno'];
            $data[$k]['user'] = $v['user_nicename'].$v['user_id'];
            $goods = '';
            foreach ($v['goods'] as $vo) {
                $goods .= $vo['name'] . ' X ' . $vo['num'] . "\r\n";
            }
            $data[$k]['goods'] = $goods;
            $data[$k]['uname'] = $v['uname'];
	$data[$k]['total_payed'] = $v['total_payed']?:0;
            $data[$k]['addr'] = $v['addr'].$v['addr_info'];
            $data[$k]['phone'] = $v['phone'];
            $data[$k]['remark'] = $v['remark'];
            $data[$k]['kdname'] = $v['kdname'];
            $data[$k]['kdno'] = $v['kdno'];
            $data[$k]['sys_remark'] = $v['sys_remark'];
        }

        $header = ['运单号','寄件人(昵称，ID)','物品明细','收货人','充值金额','地址','手机号','邮寄备注','快递公司','快递单号','系统备注'];
        $this->export($data,$header);
    }


    /**
     * 导出Excel文件
     * @param  string $savefile   文件名
     * @param  array  $fileheader 表头字段名称（传入索引数组）
     * @param  string $sheetname  sheet名称
     */
    public function export(array $data,array $fileheader, $savefile=null, $sheetname='MySheet'){

        /** Include PHPExcel */
        require_once APP_PATH.'../PHPExcel/PHPExcel.php';
        $excel = new \PHPExcel();

        if (is_null($savefile)) {
            $savefile = 'waybill';
        } else {
            //防止中文命名，下载时ie9及其他情况下的文件名称乱码
            iconv('UTF-8', 'GB2312', $savefile);
        }
        $objActSheet = $excel->getActiveSheet();
        //根据有生成的excel多少列，$letter长度要大于等于这个值
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M','N');
        //设置当前的sheet
        $excel->setActiveSheetIndex(0);
        //设置sheet的name
        $objActSheet->setTitle($sheetname);
        //设置表头
        for ($i = 0; $i < count($fileheader); $i++) {
            // 单元宽度自适应,1.8.1版本phpexcel中文支持勉强可以，自适应后单独设置宽度无效
            $objActSheet->getColumnDimension("$letter[$i]")->setAutoSize(true);
            // 设置表头值，这里的setCellValue第二个参数不能使用iconv，否则excel中显示false
            $objActSheet->setCellValue("{$letter[$i]}1", $fileheader[$i]);
            // 设置表头字体样式
            $objActSheet->getStyle("{$letter[$i]}1")->getFont()->setName('微软雅黑');
            //设置表头字体大小
            $objActSheet->getStyle("{$letter[$i]}1")->getFont()->setSize(12);
            //设置表头字体是否加粗
            $objActSheet->getStyle("{$letter[$i]}1")->getFont()->setBold(true);
            //设置表头文字垂直居中
            $objActSheet->getStyle("{$letter[$i]}1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置文字上下居中
            $objActSheet->getStyle("$letter[$i]")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置表头外的文字垂直居中
            $excel->setActiveSheetIndex(0)->getStyle("$letter[$i]")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        //单独设置D列宽度为15
        // $objActSheet->getColumnDimension('D')->setWidth(15);

        $j = 2;
        foreach ($data as $k => $v) {
            $i = 0;
            foreach ($v as $key => $value) {
                $objActSheet->getColumnDimension("$letter[$i]")->setAutoSize(true);
                $objActSheet->setCellValue($letter[$i] . $j, $value . "\t");
                $i++;
            }
            $j++;
        }

        // for ($i = 2;$i <= count($data) + 1;$i++) {
        //     foreach ($letter as $key=>$value) {
        //         $objActSheet->setCellValue("$value$i",$data[$i-2][$key]);
        //     }
        //     //设置单元格高度，暂时没有找到统一设置高度方法
        //     $objActSheet->getRowDimension($i)->setRowHeight('80px');
        // }
        header('Content-Type: application/vnd.ms-excel');
        //下载的excel文件名称，为Excel5，后缀为xls，不过影响似乎不大
         header('Content-Disposition: attachment;filename="' . $savefile . '.xls"');
         header('Cache-Control: max-age=0');
        // 用户下载excel
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }


    /**
     * 导入快递单号信息（批量发货）
     */
    public function import()
    {
        $excelfile = $_FILES['importFile']['tmp_name'];
        if( empty($excelfile) )$this->error('未上传文件','admin/waybill/lists');

        require_once APP_PATH.'../PHPExcel/PHPExcel.php';

        $reader = new \PHPExcel_Reader_Excel2007();

        if(!$reader->canRead($excelfile)){
            $reader = new \PHPExcel_Reader_Excel5();
            if(!$reader->canRead($excelfile)){
                $this->error('文件格式错误');
            }
        }

        $excel = $reader->load($excelfile);
        $sheet = $excel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        if($highestRow < 2)$this->error('文件不包含数据');

        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $data = array();
        for ($row = 2; $row <= $highestRow; $row++) {
            $data[$row] = array(
                'addr'       => trim($sheet->getCellByColumnAndRow(4, $row)->getValue()),
                'phone'      => trim($sheet->getCellByColumnAndRow(5, $row)->getValue()),
                'waybillno'  => trim($sheet->getCellByColumnAndRow(0, $row)->getValue()),
                'kdname'     => trim($sheet->getCellByColumnAndRow(7, $row)->getValue()),
                'kdno'       => trim(str_replace('	', '', (string)$sheet->getCellByColumnAndRow(8, $row)->getValue())),
                'sys_remark' => trim($sheet->getCellByColumnAndRow(9, $row)->getValue()),
            );
			
            /* 获取发货的运单包含的礼物、娃娃id */
            $waybillInfo = M('waybill as a')
                ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
                ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
                ->field('a.*,b.wawa_id,c.gift_id')
                ->where(['waybillno'=>$data[$row]['waybillno']])
                ->select();
				
			$waybillRow = current($waybillInfo);
			
            if( empty($data[$row]['kdname']) || empty($data[$row]['kdno']) ) $this->error('发货失败，必须填写快递公司和快递单号','admin/waybill/lists');
            $data[$row]['fhtime']   = $waybillRow['fhtime'] ? $waybillRow['fhtime'] : time();
            $data[$row]['status']   = $waybillRow['status'] == 1 ? 2 : $waybillRow['status'];
            M('waybill')->where(['waybillno'=>$data[$row]['waybillno']])->save($data[$row]);



            /* 更新cmf_gift表和cmf_give_gift表的出货量字段,库存字段 */
            foreach ($waybillInfo as $k => $v) {
                M('gift')->where(['id'=>$v['wawa_id']])->setInc('chuhuo');
                M('gift')->where(['id'=>$v['wawa_id']])->setDec('stock');
                M('give_gift')->where(['id'=>$v['gift_id']])->setInc('shipment_num');
                //M('give_gift')->where(['id'=>$v['gift_id']])->setDec('quantity');
            }

            /* 通过运单号获取我的娃娃表id，更新我的娃娃表状态 */
            $res = M('waybill')->where(['waybillno'=>$data[$row]['waybillno']])->select();
            foreach ($res as $k => $v) {
                if( $v['user_wawas_id'] ){
                    M('user_wawas')->where(['id'=>$v['user_wawas_id']])->save(['status'=>2]);
                }
                if( $v['user_gift_id'] ){
                    M('users_gift')->where(['id'=>$v['user_gift_id']])->save(['type'=>3]);
                }
            }

        }

        if(count($data)){
           $this->success('操作成功','admin/waybill/lists');
           exit;
        }
        $this->error('数据导入失败','admin/waybill/lists');
    }


    public function edit()
    {
        $waybillno = I('waybillno');
        if( $waybillno ){

            $userWaybillAll = M('waybill as a')
                ->join('left join cmf_users as e on a.user_id = e.id')
                ->join('left join cmf_user_wawas as b on a.user_wawas_id = b.id')
                ->join('left join cmf_users_gift as c on a.user_gift_id = c.id')
//                ->join('left join cmf_user_addr as d on a.addr_id = d.addr_id')
                ->field('a.*,b.wawa_id,c.gift_id,e.user_nicename')
                ->where(['a.waybillno'=>$waybillno])
                ->select();

            foreach ($userWaybillAll as $k => $v) {
                if( $v['wawa_id'] ){
                    $wawaname = M('gift')->where(['id'=>$v['wawa_id']])->getField('giftname');
                    $userWaybillAll[$k]['wawaname'] = $wawaname;
                    $userWaybillAll[$k]['wawa_id'] = $v['wawa_id'];
                }else{
                    $userWaybillAll[$k]['wawaname'] = '';
                }
                if( $v['gift_id'] ){
                    $giftname = M('give_gift')->where(['id'=>$v['gift_id']])->getField('name');
                    $userWaybillAll[$k]['giftname'] = $giftname;
                    $userWaybillAll[$k]['gift_id'] = $v['gift_id'];
                }else{
                    $userWaybillAll[$k]['giftname'] = '';
                }
            }

            $arr = [];
            foreach($userWaybillAll as $k=>$v){
                if(!isset($arr[$v['waybillno']])){
                    $arr[$v['waybillno']]=array(
                        'user_id'       => $v['user_id'],
                        'user_nicename' => $v['user_nicename'],
                        'waybill_id'    => $v['waybill_id'],
                        'waybillno'     => $v['waybillno'],
                        'status'        => $v['status'],
                        'remark'        => $v['remark'],
                        'ctime'         => $v['ctime'],//运单生成时间
                        'fhtime'        => $v['fhtime'],//发货时间
                        'shtime'        => $v['shtime'],//收货时间
                        'uname'         => $v['uname'],
                        'phone'         => $v['phone'],
                        'addr'          => $v['addr'],
                        'addr_info'     => $v['addr_info'],
                        'kdname'        => $v['kdname'],
                        'kdno'          => $v['kdno'],
                        'goodsname'     => $v['goodsname'],
                        'sys_remark'    => $v['sys_remark'],
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
                            $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                            $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift_id'=>$v['gift_id']);
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
                            $arr[$v['waybillno']]['goods'][] = array('name'=>$v['wawaname'],'num' => 1,'wawa_id'=>$v['wawa_id']);
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
                            $arr[$v['waybillno']]['goods'][] = array('name'=>$v['giftname'],'num' => 1,'gift'=>$v['gift_id']);
                        }
                    }
                }
            }
            $arr = array_values($arr);
            $num = M('waybill')->where(['waybillno'=>$waybillno])->count();
            $this->assign('num',$num);

            $this->assign('status_name',$this->statusArr);
            $this->assign('data',$arr);

        }
        $this->display();

    }


    public function edit_post()
    {

        if( IS_POST ){
            $waybillno = I('get.waybillno');
            $post = I('post.');
            M('waybill')->where(['waybillno'=>$waybillno])->save($post);
            $this->success('保存成功',U('Waybill/lists'));
        }

    }



}
