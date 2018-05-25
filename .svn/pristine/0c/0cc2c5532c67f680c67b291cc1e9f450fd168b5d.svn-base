<?php

/**
 * 提现
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class FeedbackController extends AdminbaseController
{
    function index()
    {

        if ($_REQUEST['status'] != '') {
            $map['status'] = $_REQUEST['status'];
            $_GET['status'] = $_REQUEST['status'];
        }
        if ($_REQUEST['start_time'] != '') {
            $map['addtime'] = array("gt", strtotime($_REQUEST['start_time']));
            $_GET['start_time'] = $_REQUEST['start_time'];
        }

        if ($_REQUEST['end_time'] != '') {

            $map['addtime'] = array("lt", strtotime($_REQUEST['end_time']));
            $_GET['end_time'] = $_REQUEST['end_time'];
        }
        if ($_REQUEST['start_time'] != '' && $_REQUEST['end_time'] != '') {

            $map['addtime'] = array("between", array(strtotime($_REQUEST['start_time']), strtotime($_REQUEST['end_time'])));
            $_GET['start_time'] = $_REQUEST['start_time'];
            $_GET['end_time'] = $_REQUEST['end_time'];
        }

        if ($_REQUEST['keyword'] != '') {
            $map['uid'] = array("like", "%" . $_REQUEST['keyword'] . "%");
            $_GET['keyword'] = $_REQUEST['keyword'];
        }
        if ( I('contact') ) {
            $map['contact'] = array("like", "%" . I('contact') . "%");
            $_GET['contact'] = $_REQUEST['contact'];
        }
        if ( I('content') ) {
            $map['content'] = array("like", "%" . I('content') . "%");
            $_GET['content'] = $_REQUEST['content'];
        }

        $feedback = M("feedback");
        $count = $feedback->where($map)->count();
        $page = $this->page($count, 20);
        $lists = $feedback
            ->where($map)
            ->order("status,addtime desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach ($lists as $k => $v) {
            $userinfo = M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
            $lists[$k]['userinfo'] = $userinfo;
        }

        $this->assign('lists', $lists);
        $this->assign('formget', $_GET);
        $this->assign("page", $page->show('Admin'));

        $this->display();
    }

    function setstatus()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $data['status'] = 1;
            $data['uptime'] = time();
            $result = M("feedback")->where("id='{$id}'")->save($data);
            if ($result) {
                $this->success('标记成功');
            } else {
                $this->error('标记失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = M("feedback")->delete($id);
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }


    function edit()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $feedback = M("feedback")->find($id);
            $feedback['userinfo'] = M("users")->field("user_nicename")->where("id='$feedback[uid]'")->find();
            $this->assign('feedback', $feedback);
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    function edit_post()
    {
        if (IS_POST) {
            if ($_POST['status'] == '0') {
                $this->error('未修改状态');
            }

            $feedback = M("feedback");
            $feedback->create();
            $feedback->uptime = time();
            $result = $feedback->save();
            if ($result) {
                $this->success('修改成功', U('Userfeedback/index'));
            } else {
                $this->error('修改失败');
            }
        }
    }


    public function export()
    {
        $s = strtotime(I('startTime'));
        $e = strtotime(I('endTime') . " 23:59:59");
        if (!empty($s) && !empty($e)) {

            $filename = './temp/feedback.' . date('Y.m.d') . '.csv';
//                $fp = fopen($filename, 'w');
            $fp = fopen('php://output', 'a');
            //fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($fp, array(
                mb_convert_encoding('ID', 'gbk', 'utf8'),
                mb_convert_encoding('会员', 'gbk', 'utf8'),
                mb_convert_encoding('联系方式', 'gbk', 'utf8'),
                mb_convert_encoding('系统版本', 'gbk', 'utf8'),
                mb_convert_encoding('手机型号', 'gbk', 'utf8'),
                mb_convert_encoding('反馈信息', 'gbk', 'utf8'),
                mb_convert_encoding('状态', 'gbk', 'utf8'),
                mb_convert_encoding('提交时间', 'gbk', 'utf8'),
                mb_convert_encoding('处理时间', 'gbk', 'utf8'),
            ));

            $where = "addtime >= " . $s;
            $where .= " and addtime < " . $e;
            $list = M('feedback as a')
                ->join('cmf_users as b on a.uid = b.id')
                ->where($where)
                ->field('a.*,b.user_nicename')
                ->select();
//                p($list);
            foreach ($list as $v) {
                fputcsv($fp,
                    array(
                        $v['id'],
                        $v['user_nicename'],
                        $v['contact'],
                        $v['version'],
                        $v['model'],
                        mb_convert_encoding($v['content'], 'gbk', 'utf8'),
                        mb_convert_encoding($v['status'] == 0 ? '处理中' : '已处理', 'gbk', 'utf8'),
                        date('Y-m-d H:i:s', $v['addtime']),
                        $v['uptime'] == 0 ? mb_convert_encoding('处理中', 'gbk', 'utf8') : date('Y-m-d H:i:s', $v['addtime']),
                    )
                );
            }

            fclose($fp);
            $fileinfo = pathinfo($filename);
            header('Content-type: application/x-' . $fileinfo['extension']);
            header('Content-Disposition: attachment; filename=' . $fileinfo['basename']);
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            exit();
        } else {
            echo '无参数,错误';
        }
    }

}
