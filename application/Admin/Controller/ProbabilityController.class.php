<?php
/**
 * 概率管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/20
 * Time: 15:11
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ProbabilityController extends AdminbaseController
{

    /**
     * 所有概率
     */
    public function index()
    {
        $r = M('config')->field('proba_all')->find(1);
        $r = json_decode($r['proba_all'],true);

        $this->assign('row',$r);
        if( IS_POST ){
            $post = I('post.');
            $post = json_encode($post);
            M('config')->where(['id'=>1])->save(['proba_all'=>$post]);
            $this->success('编辑成功');exit;
        }
        $this->display();
    }

    public function grade()
    {
        $count = M('user_grade')->count();
        $page = $this->page($count,20);
        $res = M('user_grade as a')

            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $this->assign('page',$page->show('Admin'));
        $this->assign('row',$res);
        $this->display();

    }

    public function del_grade()
    {
        $id = I('id');
        M('user_grade')->where(['id' => $id])->delete();
        $this->redirect('admin/probability/grade');
    }

    public function add_grade()
    {
        $id = I('id');
        $row = M('user_grade')->find($id);

        if (IS_POST) {
            $id = I('get.id');
            $post = I('post.');
            if($post['title']==''){
                $this->error("标题不能为空！");
            }

            $post['free_coin_strong_num'] = intval($post['free_coin_strong_num'] );
            $post['coin_strong_num'] = intval($post['coin_strong_num'] );
            if ($id)
            {
                M('user_grade')->where(['id' => $id])->save($post);

            }
            else{
                $result = M('user_grade')->add($post);
            }


            $this->success('编辑成功');
        }
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 用户贩卖模式
     */
    public function sell()
    {
        $where = ' 1=1 ';
        if( IS_POST ){
            $post = I('post.');
            if( $post['room_no'] ){
                $where .= ' and a.room_no like "%'.$post['room_no'].'%"';
                $this->assign('room_no',$post['room_no']);
            }
            if( $post['device_no'] ){
                $where .= ' and b.deveci_no like "%'.$post['device_no'].'%"';
                $this->assign('device_no',$post['device_no']);
            }
            if( $post['goodsname'] ){
                $where .= ' and c.giftname like "%'.$post['goodsname'].'%"';
                $this->assign('goodsname',$post['goodsname']);
            }
            if( $post['status'] >= 0 ){
                $where .= ' and a.status = '.$post['status'];
                $this->assign('status',$post['status']);
            }
        }
        $statusArr = ['在线','补货','维护','游戏中','离线'];
        $count = M('game_room')->count();
        $page = $this->page($count,20);
        $res = M('game_room as a')
            ->join('left join cmf_device as b on a.fac_id = b.device_unique_code')
            ->join('left join cmf_gift as c on a.type_id = c.id')
            ->where($where)
            ->field('a.*,b.deveci_no,c.giftname,c.cost,c.spendcoin')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $starttime = $this->getWeekStartTime();
        $now = time();
        //统计各房间的一周抓中次数
        $sql = "SELECT room_id,COUNT(*) AS counts FROM cmf_game_history WHERE success > 0 AND ctime >= $starttime AND ctime <= $now GROUP BY room_id";
        $mes = M('game_history')->query($sql);
        foreach ($res as $k => $v) {
            foreach ($mes as $kk => $vv) {
                if( $v['id'] == $vv['room_id'] ){
                    $res[$k]['week_count'] = $vv['counts'];
                }
            }
        }

        $this->assign('statusArr',$statusArr);
        $this->assign('page',$page->show('Admin'));
        $this->assign('row',$res);
        $this->display();
    }

    /**
     * 启用 / 停用用户贩卖模式
     */
    public function isstart(){
        if( IS_AJAX ){
            $id = I('post.id');
            if( $id ){
                $res = M('game_room')->find($id);
                $data = [];
                if( $res['is_sellmodel'] == 1 ){
                    $data['is_sellmodel'] = 0;
                }else{
                    $data['is_sellmodel'] = 1;
                }
                M('game_room')->where(['id'=>$id])->save($data);
                $this->ajaxReturn(['status'=>1]);
            }
            $this->ajaxReturn(['status'=>0]);
        }
    }

    /**
     * 启用 / 停用房间概率模式
     */
    public function isstart_room(){
        if( IS_AJAX ){
            $id = I('post.id');
            if( $id ){
                $res = M('game_room')->find($id);
                $data = [];
                if( $res['is_roommodel'] == 1 ){
                    $data['is_roommodel'] = 0;
                }else{
                    $data['is_roommodel'] = 1;
                }
                M('game_room')->where(['id'=>$id])->save($data);
                $this->ajaxReturn(['status'=>1]);
            }
            $this->ajaxReturn(['status'=>0]);
        }
    }

public function isgradestart_room(){
    if( IS_AJAX ){
        $id = I('post.id');
        if( $id ){
            $res = M('game_room')->find($id);
            $data = [];
            if( $res['is_roomgrademodel'] == 1 ){
                $data['is_roomgrademodel'] = 0;
            }else{
                $data['is_roomgrademodel'] = 1;
            }
            M('game_room')->where(['id'=>$id])->save($data);
            $this->ajaxReturn(['status'=>1]);
        }
        $this->ajaxReturn(['status'=>0]);
    }
}

    /**
     * 批量操作用户贩卖模式
     */
    public function batch_action(){
        $post = I('post.');
        if( $post ){
            $where['id'] = ['in',$post['ids']];
            M('game_room')->where($where)->save(['is_sellmodel'=>$post['action']]);
            $this->redirect('admin/probability/sell');
        }
    }


    /**
     * 用户贩卖规则列表
     */
    public function sellmodel_list(){
        $res = M('sellmodel')->select();
        $this->assign('row',$res);
        $this->display();
    }


    /**
     * 编辑贩卖规则
     */
    public function sellmodel_edit(){
        $id = I('get.id');
        $res = M('sellmodel')->find($id);
        $this->assign('row',$res);
        if( IS_POST ){
            $post = I('post.');
            M('sellmodel')->where(['id'=>$id])->save($post);
            $this->redirect('admin/probability/sellmodel_list');
        }
        $this->display();
    }


    /**
     * 删除贩卖规则
     */
    public function sellmodel_del(){
        $id = I('id');
        if( $id ){
            M('sellmodel')->where(['id'=>$id])->delete();
            $this->redirect('admin/probability/sellmodel_list');
        }
    }


    /**
     * 添加贩卖规则
     */
    public function sellmodel_add(){
        if( IS_POST ){
            $post = I('post.');
            if( $post ) M('sellmodel')->add($post);
            $this->redirect('admin/probability/sellmodel_list');
        }
    }


    /**
     * 房间概率模式
     */
    public function room()
    {
        $where = ' 1=1 ';
        if( IS_POST ){
            $post = I('post.');
            if( $post['room_no'] ){
                $where .= ' and a.room_no like "%'.$post['room_no'].'%"';
                $this->assign('room_no',$post['room_no']);
            }
            if( $post['device_no'] ){
                $where .= ' and b.deveci_no like "%'.$post['device_no'].'%"';
                $this->assign('device_no',$post['device_no']);
            }
            if( $post['goodsname'] ){
                $where .= ' and c.giftname like "%'.$post['goodsname'].'%"';
                $this->assign('goodsname',$post['goodsname']);
            }
            if( $post['status'] >= 0 ){
                $where .= ' and a.status = '.$post['status'];
                $this->assign('status',$post['status']);
            }
        }
        $statusArr = ['在线','补货','维护','游戏中','离线'];
        $count = M('game_room')->count();
        $page = $this->page($count,20);
        $res = M('game_room as a')
            ->join('left join cmf_device as b on a.fac_id = b.device_unique_code')
            ->join('left join cmf_gift as c on a.type_id = c.id')
            ->where($where)
            ->field('a.*,b.deveci_no,c.giftname,c.cost,c.spendcoin')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $starttime = $this->getWeekStartTime();
        $now = time();
        //统计各房间的一周抓中次数
        $sql = "SELECT room_id,COUNT(*) AS counts FROM cmf_game_history WHERE success > 0 AND ctime >= $starttime AND ctime <= $now GROUP BY room_id";
        $mes = M('game_history')->query($sql);
        foreach ($res as $k => $v) {
            foreach ($mes as $kk => $vv) {
                if( $v['id'] == $vv['room_id'] ){
                    $res[$k]['week_count'] = $vv['counts'];
                }
            }
        }

        $this->assign('statusArr',$statusArr);
        $this->assign('page',$page->show('Admin'));
        $this->assign('row',$res);
        $this->display();
    }


    /**
     * 获取从今天起前一周的开始时间，比如今天星期2，那就获取到上周2的时间戳
     * @return false|string
     */
    private function getWeekStartTime(){
        return strtotime('-1 week');
    }



    /**
     * 获取本周开始时间和结束时间
     * @return array
     */
    private function getThisWeekStartAndEndTime(){
        //当前日期
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first=1;
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
        //本周结束日期
        $week_end=date('Y-m-d',strtotime("$week_start +6 days"));
        return ['start'=>strtotime($week_start),'end'=>$week_end];
    }

    /**
     * 获取上周起始时间
     * @return array
     */
    private function getPreWeekStartAndEndTime(){
        $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
        $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
        return ['start'=>$beginLastweek,'end'=>$endLastweek];
    }


    public function room_edit()
    {
        $id = I('get.id');
        if( $id ) $this->assign('row',M('game_room')->find($id));
        if( IS_POST ){
            M('game_room')->where(['id'=>$id])->save($_POST);
            $this->success('修改成功');exit;
        }
        $this->display();
    }

    public function batch_setgraderoom(){
        if( IS_AJAX ){
            $post = I('post.');
            $where['id'] = ['in',$post['ids']];
            $arr = explode(",",$post['ids']);
            if (is_array($arr))
            {
                foreach ($arr as $id)
                {
                    if( $id ){
                        $res = M('game_room')->find($id);
                        $data = [];
                        if( $res['is_roomgrademodel'] == 1 ){
                            $data['is_roomgrademodel'] = 0;
                        }else{
                            $data['is_roomgrademodel'] = 1;
                        }
                        M('game_room')->where(['id'=>$id])->save($data);
                        //$this->ajaxReturn(['status'=>1]);
                    }
                }

            }
            //$res = M('game_room')->where($where)->save(['claw_count'=>$post['claw_count']]);
            //if( $res ) $this->ajaxReturn(['status'=>1]);
            $this->ajaxReturn(['status'=>1]);
        }
    }

    public function batch_setroom(){
        if( IS_AJAX ){
            $post = I('post.');
            $where['id'] = ['in',$post['ids']];
            $res = M('game_room')->where($where)->save(['claw_count'=>$post['claw_count']]);
            if( $res ) $this->ajaxReturn(['status'=>1]);
            $this->ajaxReturn(['status'=>0]);
        }
    }

}