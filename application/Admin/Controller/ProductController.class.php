<?php

/**
 * 产品分类管理
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ProductController extends AdminbaseController
{

    public function default2()
    {

    }

    //娃娃列表
    function index()
    {
        $gift_sort = M("gift_sort")->getField("id,sortname");
        $gift_sort[0] = "默认分类";
        $this->assign('gift_sort', $gift_sort);
        $map = " 1=1 ";
        $type = I('type');
        $keyword = I('keyword') ? I('keyword') : '';
        //假如是在线状态切换到在线娃娃
        $s_type = I('get.s_type');
        if($s_type){
            $gameroomModel= M('game_room');
            $gameroomInfo = $gameroomModel
                ->field('type_id')
                ->where('is_show = 1')
                ->select();
            $wawaids = array_column($gameroomInfo,'type_id');
            $wawaids = implode(',',$wawaids);
            $map .= ' and a.id in ('.$wawaids.') ';

            //查询出逾期超过寄存的娃娃
            $day = I('get.day')?I('get.day'):30;
            if(!is_numeric($day)){
                return false;
            }
            $daytime = time() - ($day * 86400);
            //$map .= ' mywawa.ctime < '.$daytime;
        }
        if ($type) {
            $map .= ' and a.type_id='.$type;
            $this->assign('type',$type);
        }
        if ($keyword !== '') {
            $map .= " and a.id='{$keyword}' or a.giftname like '%{$keyword}%'";
            // $map['id'] = array('eq', "$keyword");
            // $map['giftname'] = array('like', "%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $gift_model = M("gift as a");
        $count = $gift_model->where($map)->count();
        $page = $this->page($count, 20);
        $page->parameter;
        $lists = $gift_model
            ->join('cmf_gift_type as b on a.type_id=b.id')
            //->join('cmf_user_wawas as mywawa on mywawa.wawa_id=a.id')
            ->field('a.*,b.name')
            //->field('a.*,b.name,mywawa.wawa_id,mywawa.ctime as getwawatime')
            ->where($map)
            ->order("a.addtime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach ($lists as $k => $v) {
            $arr = explode(',',$v['convert']);
            $str = '';
            foreach ($arr as $item) {
                if( $item == 1 ){
                    $str = '不可兑换';
                    break;
                }
                if( $item == 2 )$str .= '可兑换娃娃币 , ';
                if( $item == 3 )$str .= '可兑换礼品 , ';
            }
            $lists[$k]['convert'] = $str;
            $lists[$k]['totalcount'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'status'=>0,'is_del'=>0])->count();
            //linxiaodong
            $lists[$k]['applysendcount'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'status'=>1])->count();
            $lists[$k]['hassendcount'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'status'=>2])->count();
            $lists[$k]['giveoutcount'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'status'=>3])->count();
            $lists[$k]['convertcoin'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'is_del'=>1])->count();
            $lists[$k]['convertgift'] = M('user_wawas')->where(['wawa_id'=>$v['id'],'is_del'=>2])->count();
            //统计逾期寄存的数据
            if($s_type)
                $lists[$k]['overdue_day'] = M('user_wawas')->where('wawa_id='.$v['id'].' and status=0 and is_del=0 and ctime < '.$daytime)->count();

        }
        $this->assign('s_type', $s_type);
        $this->assign('day', $day);
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));
        $this->assign('wawa_type',M('gift_type')->select());
        $this->display();
    }

    //娃娃的删除
    function del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = M("gift")->delete($id);
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    //娃娃排序
    public function listorders()
    {
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("gift")->where(array('id' => $key))->save($data);
        }

        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }


    function add()
    {
        $type_list = M('gift_type')->select();
        $gift_sort = M("gift_sort")->getField("id,sortname");
        $this->assign('gift_sort', $gift_sort);
        $this->assign('type_list', $type_list);
        $this->display();
    }

    //娃娃的添加
    function add_post()
    {
        if (IS_POST) {
            $post = I('post.');
            $gift = M("gift");
            if (in_array('', $post)) {
                $this->error('内容不能为空');
            }
            $name = $gift->where("giftname='{$post['giftname']}'")->find();
            if ($name) {
                $this->error('此娃娃的名称已存在了，请重新输入');
            }
            if (is_numeric($post['needcoin']) === false) {
                $this->error('可兑换的娃娃数量只能是数字类型');
            }
            $post['addtime'] = time();
            $result = $gift->add($post);
            if ($result) {
                $this->success('添加成功');
                exit;
            } else {
                $this->error('添加失败');
                exit;
            }
        }
    }

    function edit()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $gift = M("gift")->find($id);
            $this->assign('gift', $gift);
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    //娃娃的编辑
    function edit_post()
    {
        if (IS_POST) {
            $post = I('post.');
            $gift = M("gift");
            if (in_array('', $post)) {
                $this->error('内容不能为空');
            }
            $name = $gift->where("giftname='{$post['giftname']}'")->find();
            if ($name) {
                $this->error('此娃娃的名称已存在了，请重新输入');
            }
            if (is_numeric($post['needcoin']) === false) {
                $this->error('可兑换的娃娃数量只能是数字类型');
            }
            $result = $gift->save($post);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }
    }

    function sort_index()
    {

        $gift_sort = M("gift_sort");
        $count = $gift_sort->count();
        $page = $this->page($count, 20);
        $lists = $gift_sort
            ->where()
            ->order("orderno asc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));

        $this->display();
    }

    function sort_del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = M("gift_sort")->delete($id);
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    //排序
    public function sort_listorders()
    {

        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("gift_sort")->where(array('id' => $key))->save($data);
        }

        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    function sort_add()
    {

        $this->display();
    }

    function do_sort_add()
    {

        if (IS_POST) {
            if ($_POST['sortname'] == '') {
                $this->error('分类名称不能为空');
            }
            $gift_sort = M("gift_sort");
            $gift_sort->create();
            $gift_sort->addtime = time();

            $result = $gift_sort->add();
            if ($result) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
    }

    function sort_edit()
    {

        $id = intval($_GET['id']);
        if ($id) {
            $sort = M("gift_sort")->find($id);
            $this->assign('sort', $sort);
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    function do_sort_edit()
    {
        if (IS_POST) {
            $gift_sort = M("gift_sort");
            $gift_sort->create();
            $result = $gift_sort->save();
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }
    }

    // 娃娃分类列表
    public function type_list()
    {
        $gift_model = M("gift_type");
        $count = $gift_model->count();
        $page = $this->page($count, 20);
        $lists = $gift_model->alias('a')
            ->order("id")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));

        $this->display();
    }

    //娃娃分类的删除
    function del_type()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = M("gift_type")->delete($id);
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    // 娃娃分类添加
    public function type_add()
    {
        if (IS_POST) {

            $gift_type = M('gift_type');

            $post = I('post.');
            if (!$post['name']) {
                $this->error('娃娃分类名称不能为空');
            }
            $name = $gift_type->where("name='{$post['name']}'")->find();
            if ($name) {
                $this->error('此娃娃的名称已存在了，请重新输入');
            }

            if ($post['id']) {
                if ($gift_type->where('id=' . $post['id'])->save($post)) {
                    $this->success('修改成功', U('type_list'));
                    exit;
                }
            } else {
                if ($gift_type->add($post)) {
                    $this->success('添加成功', U('type_list'));
                    exit;
                } else {
                    $this->error('添加失败');
                    exit;
                }
            }
        }

        $id = intval(I('id'));
        if ($id) {
            $arrInfo = M('gift_type')->where('id=' . $id)->find();
            $this->assign('arrInfo', $arrInfo);
        }

        $this->display();
    }

}
