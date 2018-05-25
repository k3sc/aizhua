<?php
/**
 * 活动设置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/19
 * Time: 18:57
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ActiveconfigController extends AdminbaseController
{

    // 上币活动列表
    public function index()
    {

        // 页面筛选
        $where = '1=1';
        $post = I('post.');
        if ($post['id']) { // 活动id
            $where .= ' and id=' . intval($post['id']);
        } else {
            // 启用时间
            if ($post['sdate'] && $post['edate']) {
                $where .= ' and atime >= ' . intval(strtotime($post['sdate']));
                $where .= ' and atime <= ' . intval(strtotime($post['edate'] . ' 23:59:59'));
            }
            // 活动状态
            if (isset($post['status']) && $post['status'] != '') {
                $where .= ' and status=' . $post['status'];
            }
            // 活动标题
            if ($post['title']) {
                $where .= ' and title like "%' . $post['title'] . '%"';
            }
        }

        $count = M('activity')->where($where)->count();
        $page = $this->page($count, 20);
        $list = M('activity')
            ->where($where)
            ->order("ctime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign("list", $list);
        $this->assign("page", $page->show('Admin'));
        $this->display();
    }

    // 添加/编辑上币活动
    public function add_act()
    {

        if (IS_POST) {
            $post = I('post.');
            $post['title'] = trim($post['title']);
            if (empty($post['title'])) {
                $this->error("活动标题不能为空！");
            }
            $post['about'] = trim($post['about']);
            if (empty($post['about'])) {
                $this->error("活动简介不能为空！");
            }
            $post['coin'] = intval($post['coin']);
            if (empty($post['coin'])) {
                $this->error("赠送金币数不能为空或者非数字！");
            }
            if (intval($post['type']) == 1) {
                $post['users'] = trim($post['users']);
                if (empty($post['users'])) {
                    $this->error("参与活动用户不能为空！");
                }
            }

            if (empty($post['id'])) {
                $post['ctime'] = time();
                if (M('activity')->add($post)) {
                    $this->success("活动添加成功！", U('index'));
                }
            } else {
                if (M('activity')->where('id=' . $post['id'])->save($post)) {
                    $this->success("活动修改成功！", U('index'));
                }
            }
        }

        $id = intval(I('id'));
        if ($id) {
            // 读取活动信息
            $arrAct = M('activity')->field('*')->where('id=' . $id)->find();
            $this->assign("arrAct", $arrAct);
        }

        $this->display();
    }

    // 删除单条上币活动
    public function del()
    {
        $id = intval(I('id'));
        if (empty($id)) {
            $this->error("删除记录的id不能为空！");
        }
        if (M('activity')->where('id=' . $id)->delete()) {
            $this->success("删除活动成功！", U('index'));
        } else {
            $this->error("删除活动失败！");
        }
    }

    // 启用上币活动进行送币
    public function action()
    {
        $id = intval(I('id'));
        if (empty($id)) {
            $this->error("活动id不能为空！");
        }
        // 读取活动信息
        $arrAct = M('activity')->field('title, status, type, users, coin, about')->where('id=' . $id)->find();

        if ($arrAct['status'] == 1) {
            $this->error("该活动已失效！");
        }
        // 判断是所有用户或者是部分用户
        if ($arrAct['type'] == 1) {  // 部分用户

            // 重新组合,防止错误信息传递过来造成sql错误
            /* 把中文逗号替换成英文逗号 */
            $arrAct['users'] = str_replace('，', ',', $arrAct['users']);
            /* 去除中间空格 */
            $arrAct['users'] = str_replace(' ','',$arrAct['users']);
            /* 去除首尾空格 */
            $arrAct['users'] = ltrim($arrAct['users']);
            $arrAct['users'] = rtrim($arrAct['users']);
            /* 去除首尾逗号 */
            $arrAct['users'] = trim($arrAct['users'],',');

            $arrTemp = explode(',', $arrAct['users']);
            $arrTemp = array_filter($arrTemp);
            $strTemp = implode(',', $arrTemp);

            // 获取用户
            $arrList = M('users')->field('id')->where('id in(' . $strTemp . ')')->select();
        } else { // 所有用户
            // 获取用户
            $arrList = M('users')->field('id')->select();
        }
        set_time_limit(0);
        // 遍历用户进行送币和添加消息提醒
        foreach ($arrList as $v) {
            // 送币
            M('users')->where('id=' . intval($v['id']))->setInc('coin', $arrAct['coin']);
            M('users')->where('id=' . intval($v['id']))->setInc('active_coin', $arrAct['coin']);
            // 发送消息提醒
            $arrNotice = array();
            $arrNotice['user_id'] = $v['id'];
            $arrNotice['type'] = 5;
            $arrNotice['title'] = $arrAct['title'];
            $arrNotice['content'] = $arrAct['about'];
            $arrNotice['desc'] = $arrAct['about'];
            $arrNotice['ctime'] = time();
            M('notice')->add($arrNotice);
            /* 友盟推送 */
            $row = M('users')->where(['id'=>$arrNotice['user_id']])->field('androidtoken,iphonetoken')->find();
            $this->umengpush( $row['androidtoken'],$row['iphonetoken'],$arrNotice['title'],$arrNotice['content'] );
            /* 流水记录 */
            $this->add_coinrecord($v['id'],'income','active_shangbi',$arrAct['coin'],0);
        }
        // 活动设置为已启用
        M('activity')->where('id=' . $id)->save(array('status' => 1, 'atime' => time()));
        $this->success("送币活动启用成功,有关用户送币完成！", U('index'));
    }

    // 定时活动列表
    public function timing_list()
    {
        // 页面筛选
        $where = '1=1';
        $post = I('post.');
        if ($post['id']) { // 活动id
            $where .= ' and id=' . intval($post['id']);
        } else {
            // 活动开始/结束时间
            if ($post['start_date'] && $post['end_date']) {
                $where .= ' and start_date >= ' . intval(strtotime($post['start_date'].' 00:00:00'));
                $where .= ' and end_date <= ' . intval(strtotime($post['end_date'] . ' 23:59:59'));
            }
            // 活动状态
            if (isset($post['status']) && $post['status'] != '') {
                $where .= ' and status=' . $post['status'];
            }
            // 活动类型
            if ($post['type']) {
                $where .= ' and type=' . $post['type'];
            }
            // 活动标题
            if ($post['title']) {
                $where .= ' and title like "%' . $post['title'] . '%"';
            }
        }

        /* 检测失效的活动 */
        M('timing_activity')->where(['end_date'=>['lt',strtotime(date('Y-m-d 23:59:59'))]])->save(['status'=>3]);
        M('timing_activity')->where(['status'=>3,'end_date'=>['egt',time()]])->save(['status'=>0]);

        $count = M('timing_activity')->where($where)->count();
        $page = $this->page($count, 20);
        $list = M('timing_activity')
            ->where($where)
            ->order("ctime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign("list", $list);
        $this->assign("page", $page->show('Admin'));
        $this->display();
    }

    // 添加/编辑定时任务
    public function set_timing()
    {

        if (IS_POST) {
            $post = I('post.');
            $post['title'] = trim($post['title']);
            if (empty($post['title'])) {
                $this->error("活动标题不能为空！");
            }
            $post['about'] = trim($post['about']);
            if (empty($post['about'])) {
                $this->error("活动简介不能为空！");
            }
            if (empty($post['start_date'])) {
                $this->error("开始日期不能为空！");
            }
            if (empty($post['end_date'])) {
                $this->error("结束日期不能为空！");
            }
            if (intval($post['type']) == 2) { // 周期
                if (empty($post['is_every_day'])) { // 非每天
                    if (empty($post['week'])) {
                        $this->error("活动周几不能为空！");
                    }
                    $post['is_every_day'] = 0;
                    $post['str_week'] = implode(',', $post['week']);
                } else {
                    $post['str_week'] = '';
                }
            }
            unset($post['week']);
            $post['coin'] = intval($post['coin']);
            if (empty($post['coin'])) {
                $this->error("赠送金币数不能为空或者非数字！");
            }
            $post['start_date'] = strtotime($post['start_date'].' 00:00:00');
            $post['end_date'] = strtotime($post['end_date'].' 23:59:59');
            if (empty($post['id'])) {
                $post['ctime'] = time();
                if ( $id = M('timing_activity')->add($post) ) {
                    $this->success("定时活动添加成功！", U('timing_list'));
                }
            } else {
                if (M('timing_activity')->where('id=' . $post['id'])->save($post)) {
                    $this->success("定时活动修改成功！", U('timing_list'));
                }
            }
        }

        $id = intval(I('id'));
        if ($id) {
            // 读取活动信息
            $arrAct = M('timing_activity')->field('*')->where('id=' . $id)->find();
            $this->assign("arrAct", $arrAct);
        }

        $arrDate = array('周一', '周二', '周三', '周四', '周五', '周六', '周日');
        $this->assign("arrDate", $arrDate);
        $this->display();
    }

    // 启用或停止定时活动
    function action_timing()
    {
        $arr['status'] = intval(I('status'));
        $arr['id'] = intval(I('id'));
        if (isset($arr['status']) && $arr['id']) {
            if (M('timing_activity')->where('id=' . $arr['id'])->save($arr)) {
                $this->success("操作成功！", U('timing_list'));
            }
        }
    }

    /**
     * 删除定时活动
     */
    public function delete(){
        $id = I('id');
        if( $id ){
            M('timing_activity')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }
    }

    // 其他活动设置
    public function other()
    {
        if (IS_POST) {
            $post = I('post.');
            $post['sdate'] = strtotime($post['sdate']);
            $post['edate'] = strtotime($post['edate']);
            M('active_config')->save($post);
            $this->success("操作成功！");
        }

        $arrAct = M('active_config')->find();

        $this->assign("arrAct", $arrAct);
        $this->display();
    }



}