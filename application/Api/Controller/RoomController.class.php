<?php
/**
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/12
 * Time: 11:09
 */

namespace Api\Controller;

use Admin\Controller\UsersController;
use Think\Log;

class RoomController extends BaseController
{

    private $redis;

    public function api()
    {
        $api_name = I('api_name');
        if (!method_exists($this, $api_name)) $this->_return(404, '接口不存在');
        include_once THINK_PATH . '../../simplewind/Lib/Extend/TPRedis.php';
        $this->redis = new \TPRedis(array('host' => C('REDIS_HOST'), 'auth' => C('REDIS_AUTH'), 'prefix' => C('REDIS_PREFIX'),"format"=>"json"));
//$this->redis->set('1', '222');
//echo $this->redis->get('1');
//exit;	
        $this->$api_name();
    }

    public function room_info()
    {
        $room_id = intval(I('room_id'));
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        $strWhere = 'a.id=' . $room_id;
        $data = M('game_room')
            ->alias('a')
            ->join('cmf_gift as b on a.type_id=b.id', 'left')
            ->join('left join cmf_bgmusic as c on a.bgmusic_id = c.id')
            ->field('a.*,b.giftname,b.gifticon,b.img,b.spendcoin,b.needcoin,b.convert,c.addr as bgmusic')
            ->where($strWhere)
            ->find();
			
		//踢掉不在线观众
		$sql = "delete a from cmf_game_audience a left join cmf_user_online as b on a.user_id=b.user_id where b.user_id is null or b.ctime<".(time() - 120);
		M()->execute($sql);
		//M('game_audience')->alias('a')->join('cmf_user_online as b on a.user_id=b.user_id')->where('b.ctime<'.(time() - 120))->delete();
			
        $audience_users = (array)@array_filter(M('game_audience')->alias('a')->join('cmf_users as b on a.user_id=b.id')->where('a.room_id=' . $room_id)->getField('user_id', true));
		$audience_total = count($audience_users);
		if(!in_array($this->user_id, $audience_users) && $audience_total >= $data['max_user'])$this->_return(-1002, '房间人数超过上限');
		$data['audience_total'] = $audience_total + 1;
        $data['my_wait_rownum'] = $this->roomwait_rownum($room_id);
        //进行中的游戏
        //$data['now_game'] = M('game_history')->where('room_id='.$room_id.' and status<2')->find();
        $data['now_game'] = $this->redis->get('roomgame_' . $room_id);
		$gamelock = $this->redis->get('gamelock_' . $room_id);
		if($gamelock && time() >= $gamelock['time']){
			$this->redis->delete('gamelock_' . $room_id);
			$gamelock = null;
		}
        //排队总人数//如果有人正在玩+1
        $count = $this->redis->lSize('roomwaits_' . $room_id);
        $data['all_wait_num'] = $count + ($gamelock || $data['now_game'] ? 1 : 0);
        //$data['all_wait_num'] = M('game_roomwait')->where("room_id=".$room_id)->order("id asc")->count() + ($data['now_game'] ? 1 : 0);
        //我前面的人数//如果有人正在玩并且我没预约且没有人排队则+1//如果我有预约不管有没人在玩都是自己位置-1
        $data['front_wait_num'] = $data['my_wait_rownum'] == 0 ? ($data['all_wait_num'] + (!$data['all_wait_num'] && ($gamelock || $data['now_game']) ? 1 : 0)) : ($data['my_wait_rownum'] - 1);
        if($gamelock['user_id'] == $this->user_id)$data['front_wait_num'] = 0;//锁定的是自己
		$data['now_user'] = $this->redis->get('gameuser_' . $room_id);
        if ($data['img']) $data['img'] = array_filter(explode(',', $data['img']));
        if (!$data['img']) $data['img'] = array();
        $data['img'] = array_map(array($this, 'get_upload_path'), $data['img']);

        $gaid = M('game_audience')->where('user_id=' . $this->user_id)->getField('id');
        if (!$gaid) {
            M('game_audience')->add(array('user_id' => $this->user_id, 'room_id' => $room_id, 'ctime' => time()));
        } else {
            M('game_audience')->where(['id' => $gaid])->save(array('room_id' => $room_id, 'ctime' => time()));
        }

        /* 匹配房间游戏音效 */
        $yx = M('game_audio')->select();
        foreach ($yx as $k => $v) {
            if ($v['id'] == $data['yx_anniu']) {
                $data['yx_anniu'] = $v['addr'];
            }
            if ($v['id'] == $data['yx_chenggong']) {
                $data['yx_chenggong'] = $v['addr'];
            }
            if ($v['id'] == $data['yx_daojishi']) {
                $data['yx_daojishi'] = $v['addr'];
            }
            if ($v['id'] == $data['yx_kaishi']) {
                $data['yx_kaishi'] = $v['addr'];
            }
            if ($v['id'] == $data['yx_shibai']) {
                $data['yx_shibai'] = $v['addr'];
            }
            if ($v['id'] == $data['yx_xiazhua']) {
                $data['yx_xiazhua'] = $v['addr'];
            }
        }

        $this->_return(1, '获取房间信息成功', $data);
    }
	
	public function roomwait_num($room_id){
        $now_game = $this->redis->get('roomgame_' . $room_id);
		$gamelock = $this->redis->get('gamelock_' . $room_id);
		if($gamelock && time() >= $gamelock['time']){
			$this->redis->delete('gamelock_' . $room_id);
			$gamelock = null;
		}
        //排队总人数//如果有人正在玩+1
        $count = $this->redis->lSize('roomwaits_' . $room_id);
		$count = $count + ($gamelock || $now_game ? 1 : 0);
		$this->redis->set('roomnums_'.$room_id, $count);
		return $count;
	}

    //新增预约排队
    public function roomwait_edit()
    {
        $room_id = intval(I('room_id'));
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        $mywait = $this->redis->get('mywait_' . $this->user_id);

        $count = false;
        if ($mywait['room_id']) {
            if ($mywait['room_id'] == $room_id) $count = true;
            //删除已经预约的
            $this->redis->delete('mywait_' . $this->user_id);
            $this->redis->lRemove('roomwaits_' . $mywait['room_id'], $this->user_id);
        }
        //$strWhere = 'user_id='.$this->user_id;
        //M('game_roomwait')->where($strWhere)->delete();
        //
        //$strWhere = 'room_id='.$room_id.' and user_id='.$this->user_id;
        //$count = M('game_roomwait')->field('*')->where($strWhere)->count();
        if ($count) {
			$this->notice_gameover($room_id, json_encode(array("success" => 0, "user_id" => 0, "user_nicename" => "", "game_id" => 0, "room_id" => $room_id, "type" => 10)));
            $this->_return(1, '取消预约成功');
        } else {
            $gift = M('game_room')->alias('a')->join('cmf_gift as b on a.type_id=b.id', 'left')->field('b.id as wawa_type_id,b.giftname,b.spendcoin')->where("a.id=" . $room_id)->find();
            if (!$gift) $this->_return(-1003, '娃娃数据不存在');
            if ($gift['spendcoin'] > $this->user['coin']) $this->_return(-1004, '娃娃币不足，请充值');

            $this->redis->set('mywait_' . $this->user_id, array('room_id' => $room_id, 'user_id' => $this->user_id, 'tim_uid' => $this->user['tim_uid'], 'ctime' => time()));
            $this->redis->rPush('roomwaits_' . $room_id, $this->user_id);
            //M('game_roomwait')->add(array('room_id' => I('room_id'), 'user_id' => $this->user_id, 'tim_uid' => $this->user['tim_uid'], 'ctime' => time()));
            $this->_return(1, '预约抓娃娃成功');
        }
    }
	
	public function room_game_cancel(){
        $room_id = intval(I('room_id'));
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
		$this->redis->delete('mywait_' . $this->user_id);
		$this->redis->lRemove('roomwaits_' . $room_id, $this->user_id);		
		$this->redis->delete('gamelock_' . $room_id);
		$this->notice_gameover($room_id, json_encode(array("success" => 0, "user_id" => 0, "user_nicename" => "", "game_id" => 0, "room_id" => $room_id, "type" => 10)));
        $roomgame = $this->redis->get('roomgame_' . $room_id);
		if(!$roomgame){
			$roomwaits = (array)$this->redis->lRange('roomwaits_' . $room_id, 0, 0);
			foreach ($roomwaits as $v) {
				$mywait = $this->redis->get('mywait_' . $v);
				if (!$mywait['tim_time'] && $mywait['tim_uid']) {
					$this->notice_gameover('0', '{"type":11,"user_id":' . $v . ',"room_id":' . $room_id . ', "timestamp": "' . microtime() . '"}');
					$mywait['tim_time'] = time();
					$this->redis->set('mywait_' . $v, $mywait);//更新
				}
				break;
			}
		}
		$this->_return(1, '取消游戏成功');
	}

    //开始抓娃娃
    public function room_game_start()
    {

        $room_id = intval(I('room_id'));
        $this->redis->set('roomwaitstat' .$room_id , time());//更新
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        //$history = M('game_history')->where('room_id='.$room_id.' and status<2')->find();
        $history = $this->redis->get('roomgame_' . $room_id);
        if ($history) {
            if ($history['user_id'] != $this->user_id) $this->_return(-1002, '已经有人在抓娃娃了');
            $this->_return(1, '开始抓娃娃成功', $history);
        }
		$gamelock = $this->redis->get('gamelock_' . $room_id);
		if($gamelock && time() >= $gamelock['time']){
			$this->redis->delete('gamelock_' . $room_id);
			$gamelock = null;
		}
		if($gamelock && $gamelock['user_id'] != $this->user_id){
			$this->_return(-1002, '已经有人在抓娃娃了');
		}
		$this->redis->delete('gamelock_' . $room_id);
        $gift = M('game_room')->alias('a')->join('cmf_gift as b on a.type_id=b.id', 'left')->field('a.fac_id,b.id as wawa_type_id,b.giftname,b.spendcoin,b.gifticon')->where("a.id=" . $room_id)->find();
        if (!$gift['wawa_type_id']) {
            //清掉排队
           $mywait = $this->redis->get('mywait_' . $this->user_id);
            if ($mywait) {
               $this->redis->delete('mywait_' . $this->user_id);
                $this->redis->lRemove('roomwaits_' . $mywait['room_id'], $this->user_id);
           }
            //M('game_roomwait')->where("room_id=".$room_id." and user_id=".$this->user_id)->delete();
            $this->_return(-1003, '娃娃数据不存在');
        }
        if ($gift['spendcoin'] > $this->user['coin']) $this->_return(-1004, '娃娃币不足，请充值');
        $time = time();
        // 发送抓娃娃命令


        $res=S($room_id.'locker');
        if($res){
            $userid=substr($res,0,strlen($res)-10);
            $timetemp=substr($res,-10);
            if(strlen($res)>10 && $this->user_id!=$userid && ($timetemp+5>time())){
                $this->_return(-1002, '已经有人在抓娃娃了！');
            }
        }else{
            S($room_id.'locker',$this->user_id.time());
        }

        $bool = $this->start($room_id, $time);




        if (!$bool) $this->_return(-1005, '开始抓娃娃失败!');

        //清掉排队
        //M('game_roomwait')->where("user_id=".$this->user_id)->delete();
        $mywait = $this->redis->get('mywait_' . $this->user_id);
        if ($mywait) {
            $this->redis->delete('mywait_' . $this->user_id);
            $this->redis->lRemove('roomwaits_' . $mywait['room_id'], $this->user_id);
        }
        $gtime = M('device')->where('device_unique_code=' . $gift['fac_id'])->getField('game_time');
        $data = array('room_id' => $room_id, 'user_id' => $this->user_id, 'name' => $gift['giftname'], 'img' => $gift['gifticon'], 'gametime' => $gtime ? $gtime : 30, 'ctime' => $time,'coin' => $gift['spendcoin']);
        if($bool < 3)$data['is_strong'] = $bool;//强抓力
		$id = M('game_history')->add($data);
        M('game_room')->where('id=' . $room_id)->save(array('status' => 3));
		$this->notice_gameover('0', '{"type":15,"room_ids":"'.$room_id.'","status":3}');
        //扣除金币
		$coingive = 0;
        $this->user['coin'] -= $gift['spendcoin'];
		if($this->user['coin_sys_give'] > 0){
			if($gift['spendcoin'] >= $this->user['coin_sys_give']){
				$coingive = $this->user['coin_sys_give'];
				$this->user['coin_sys_give'] = 0;
			}else{
				$coingive = $gift['spendcoin'];
				$this->user['coin_sys_give'] -= $gift['spendcoin'];
			}
		}
        /* 更新用户余额 消费 */
		$update = array();
		$update['coin'] = array('exp', 'coin-'.$gift['spendcoin']);
		$update['consumption'] = array('exp', 'consumption+'.$gift['spendcoin']);
		$update['coin_sys_give'] = $this->user['coin_sys_give'];
        M("users")->where('id=' . $this->user_id)->save($update);
        //消费记录
        $insert = array("type" => 'expend', "action" => 'zhuawawa', 
			"uid" => $this->user_id, 
			"touid" => 0, 
			"giftid" => $gift['wawa_type_id'], 
			"giftcount" => 0, 
			"totalcoin" => $gift['spendcoin'], 
			"givecoin" => $coingive,
			"showid" => $id, 
			"addtime" => $time,
			'room_id' => $room_id,
			'realmoney' => ($gift['spendcoin']-$coingive)/10,
			'givemoney' => $coingive/10,
		);
        M('users_coinrecord')->add($insert);

        $history = M('game_history')->where('id=' . $id)->find();
	
        // 读取设备信息
        $arrFacInfo           = M('device as a')->join('cmf_game_config as c on a.game_config_id=c.id', 'left')->field('c.*')->where('a.device_unique_code="' . $gift['fac_id'] . '"')->find();
        $history['tcpIP']='120.79.57.214';//服务器IP
        $history['tcpPort']='5188';//服务端口
        $history['webPort']='5189';//websocket服务端口
        $history['move_time'] = $arrFacInfo['qh_time'];//天车移动时间
        $history['top_time']  = $bool < 3 ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'];//天车到顶保持爪力时间
        $history['fac_id']    = $gift['fac_id'];//设备号
        $history['mactype']   = 1;

        $this->redis->set('roomgame_' . $room_id, $history);
        $this->redis->set('gameuser_' . $room_id, array('id' => $this->user_id,
            'user_nicename' => $this->user['user_nicename'],
            'avatar' => $this->get_upload_path($this->user['avatar']),
            'avatar_thumb' => $this->user['avatar_thumb'],
        ));
        S($room_id.'locker',null);

        $this->_return(1, '开始抓娃娃成功', $history);
    }

    //抓娃娃命令
    //$post['cmd']: rotate left right up down grab
    public function room_game_cmd()
    {
        $room_id = intval(I('room_id'));
        $cmd = I('cmd');
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        if (!$cmd) $this->_return(-1002, '命令发送不正确');
		$history = $this->redis->get('roomgame_'.$room_id);
        //$history = M('game_history')->where('room_id=' . $room_id . ' and user_id=' . $this->user_id . ' and status<2')->order('id desc')->find();
        if (!$history) $this->_return(-1003, '游戏已经结束了');
        //#####################

        if ($cmd != 'rotate') { // 左右前后

            if ($cmd == 'grab') {
                $arrSet = array();
                $arrSet['status'] = 1;
                M('game_history')->where('id=' . $history['id'])->save($arrSet);
            }

            $bool = $this->move($room_id, $history['ctime'], $cmd, $history['is_strong']);
        } else { //甩抓
            if ($this->user['claw'] <= 0) {
                $config = M('config')->field('claw_coin')->find();
                if ($config['claw_coin'] && $this->user['coin'] < $config['claw_coin']) {
                    $this->_return(-1004, '余额不足甩爪失败', $history);
                }
            }
            $bool = $this->throw_paw($room_id, $history['ctime'], $history['is_strong']);

            if ($bool) {
                if ($config['claw_coin']) {
                    $this->user['coin'] -= $config['claw_coin'];
                    /* 更新用户余额 消费 */
                    M("users")->where('id=' . $this->user_id)->setDec("coin", $config['claw_coin']);
                    M("users")->where('id=' . $this->user_id)->setInc("consumption", $config['claw_coin']);
                    //消费记录
                    $insert = array("type" => 'expend', "action" => 'claw', "uid" => $this->user_id, "touid" => 0, "giftid" => 0, "giftcount" => 0, "totalcoin" => $config['claw_coin'], "showid" => $history['id'], "addtime" => time());
                    M('users_coinrecord')->add($insert);
                } else {
                    $this->user['claw'] -= 1;
                    M("users")->where('id=' . $this->user_id)->setDec("claw", 1);
                }
            }

        }
        if ($bool) {
            $this->_return(1, '命令发送成功', $history);
        } else {
            $this->_return(-1005, '命令发送失败', $history);
        }
    }

    // 游戏记录列表
    public function room_history()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;

        $strWhere = 'u.id is not null and h.success>0 and h.room_id=' . intval(I('room_id'));

        $list = M('game_history')->alias('h')->join('cmf_users as u on u.id=h.user_id', 'left')->field('h.id as hid, h.room_id, h.user_id, h.img, h.success, h.ctime, h.video,u.user_nicename,u.avatar_thumb')->where($strWhere)->order('h.id desc')->page($page, $size)->select();
        if ($list) {
            foreach ($list as &$v) {
                $v['passtime'] = $this->passtime($v['ctime']);
                $v['avatar_thumb'] = $this->get_upload_path($v['avatar_thumb']);
            }
            unset($v);
        }
        $this->_return(1, '获取历史记录成功', $list);
    }

    public function room_service_text()
    {
        $list = M('service_text')->select();
        $this->_return(1, '获取信息成功', $list);
    }

    public function room_game_service()
    {
        $room_id = intval(I('room_id'));
        $service_text_id = intval(I('service_text_id'));
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        if (!$service_text_id) $this->_return(-1002, '文档ID不能为空');
        $service_text = M('service_text')->where(['id' => $service_text_id])->find();
        if (!$service_text) $this->_return(-1003, '文档内容不存在');
        $coin = $service_text['coin'];
        if ($coin > $this->user['coin']) $this->_return(-1004, '娃娃币不足，请充值');
        $data = array('room_id' => $room_id, 'user_id' => $this->user_id, 'coin' => $coin, 'ctime' => time(), 'type' => 0, 'content' => $service_text['content']);
        $newid = M('game_service')->add($data);
        if ($coin) {
			//扣除金币
			$coingive = 0;
			$this->user['coin'] -= $coin;
			if($this->user['coin_sys_give'] > 0){
				if($coin >= $this->user['coin_sys_give']){
					$coingive = $this->user['coin_sys_give'];
					$this->user['coin_sys_give'] = 0;
				}else{
					$coingive = $coin;
					$this->user['coin_sys_give'] -= $coin;
				}
			}
			/* 更新用户余额 消费 */
			$update = array();
			$update['coin'] = array('exp', 'coin-'.$coin);
			$update['consumption'] = array('exp', 'consumption+'.$coin);
			$update['coin_sys_give'] = $this->user['coin_sys_give'];
			M("users")->where('id=' . $this->user_id)->save($update);
            //消费记录
            $insert = array("type" => 'expend', 
				"action" => 'service', 
				"uid" => $this->user_id, "touid" => 0, 
				"giftid" => 0, "giftcount" => 0, "totalcoin" => $coin, "givecoin" => $coingive,
				"showid" => $newid, "addtime" => time(),
				'realmoney' => ($coin-$coingive)/10,
				'givemoney' => $coingive/10,
				);
            M('users_coinrecord')->add($insert);
        }
        //发短信
        $config_phone = M('config')->getField('phone');
        if ($config_phone) {
            /* 获取房间信息  设备信息 */
            $info = M('game_room as a')
                ->join('left join cmf_device as b on a.fac_id = b.device_unique_code')
                ->field('a.room_name,b.deveci_no,b.device_unique_code')
                ->where(['a.id' => $room_id])
                ->find();
            $content = "<人工服务>设备：" . $info['deveci_no'] . "，服务：" . $service_text['content'] . "，用户名：" . $this->user['user_nicename'] . "，房间：" . $info['room_name'];
            sendSMS($config_phone, $content);
    }

			$fac_id = M('game_room')->where('id='.$room_id)->getField('fac_id');
			$this->_set_light($fac_id,1,630,0,1);


        $this->_return(1, '您的需求已经通知工作人员，请耐心等待。');
    }

    public function room_video_upload()
    {
        $room_id = intval(I('room_id'));
        $game_id = intval(I('game_id'));
        if (!$room_id) $this->_return(-1001, '房间ID不能为空');
        $res = $this->uploadOne('video', 'video');
        if ($res) {
            M('game_history')->where('id=' . $game_id)->save(array('video' => $res));
            $this->_return(1, '视频上传成功');
        } else {
            $this->_return(-1001, $res['error']);
        }
    }

    public function roomwait_clean()
    {
		$room_ids = array();
		$room_nums = array();
		$keys = $this->redis->keys('roomnums_*');
		if($keys){
			foreach($keys as $key){
				$k = explode('_', $key);
				$room_id = end($k);
				$count = $this->redis->get('roomnums_'.$room_id);
				$newcount = $this->roomwait_num($room_id);
				if($count != $newcount){
					$room_ids[] = $room_id;
					$room_nums[] = $newcount;
				}
			}
		}
		if($room_ids){
            $this->notice_gameover('0', '{"type":18,"room_ids":"'.implode(',', $room_ids).'","room_nums":"'.implode(',', $room_nums).'"}');
		}
		
        $keys = $this->redis->keys('gamelock_*');
        if ($keys) {
            foreach ($keys as $key) {				
				$gamelock = $this->redis->get($key);
				if($gamelock && time() >= $gamelock['time']){
                    $this->redis->delete($key);
					$k = explode('_', $key);
					$room_id = end($k);
					$this->notice_gameover($room_id, json_encode(array("success" => 0, "user_id" => 0, "user_nicename" => "", "game_id" => 0, "room_id" => $room_id, "type" => 10)));
				}
			}
		}		
        $keys = $this->redis->keys('roomwaits_*');
        if ($keys) {
            foreach ($keys as $key) {
                $temp = null;
                $k = explode('_', $key);
                $room_id = end($k);
                $roomwaits = (array)$this->redis->lRange('roomwaits_' . $room_id);
                $roomgame = $this->redis->get('roomgame_' . $room_id);
				$gamelock = $this->redis->get('gamelock_' . $room_id);
				if($gamelock && time() >= $gamelock['time']){
					$this->redis->delete('gamelock_' . $room_id);
					$gamelock = null;
				}
                foreach ($roomwaits as $v) {//轮询房间等待人数
                    $mywait = $this->redis->get('mywait_' . $v);
                    if ($mywait['tim_time'] && time() - $mywait['tim_time'] > 5) {//判断删除超时预约
                        $this->redis->lRemove('roomwaits_' . $mywait['room_id'], $v);
                        $this->redis->delete('mywait_' . $v);
                        continue;
                    }
                    if (!$temp) $temp = $mywait;
                }
                $roomwaitstat = $this->redis->get('roomwaitstat' . $room_id);
                if (!$gamelock && !$roomgame && $temp && !$temp['tim_time'] && $roomwaitstat && time()-$roomwaitstat>1) {
                    $temp['tim_time'] = time();
                    $this->redis->set('mywait_' . $temp['user_id'], $temp);//更新
                    $this->notice_gameover('0', '{"type":11,"user_id":' . $temp['user_id'] . ',"room_id":' . $temp['room_id'] . ', "timestamp": "' . microtime() . '"}');
                }
            }
        }
        $keys = $this->redis->keys('roomgame_*');
        if ($keys) {
            foreach ($keys as $key) {
                $k = explode('_', $key);
                $k = end($k);
                if ($k) {
                    $roomgame = $this->redis->get('roomgame_' . $k);
                    if ($roomgame && ($roomgame['ctime'] + $roomgame['gametime'] + 10) < time()) {
						$macno = M('game_room')->where("id=$k")->getField('fac_id');
						$_POST = array(
							'macno' => $macno,
							'sysnum' => $roomgame['ctime'],
							'gift' => 0,
							'status' => 0,
						);
						$this->success();  
                    }
                }
            }
        }
        $last_beat_time = $this->redis->get('last_beat_time');
        if ($last_beat_time < time() - 15) {
            M('device')->where('beat_time < ' . (time() - 30))->save(['status' => 2]);
            $where = 'status not in (1,2,4) and beat_time < ' . (time() - 30);
            $room_ids = M('game_room')->where($where)->getField('id', true);
            M('game_room')->where($where)->save(['status' => 4]);
            $this->redis->set('last_beat_time', time());

            if($room_ids)$this->notice_gameover('0', '{"type":15,"room_ids":"'.implode(',', $room_ids).'","status":4}');
        }
        $this->_return();
    }

    //排队位置
    //0 没有排队，显示预约按钮
    //1 显示开始游戏按钮
    //2 以上显示取消预约按钮//当前位置
    //  正在游戏请用now_game user_id 判断
    private function roomwait_rownum($room_id = 0)
    {
        if (!$room_id || !is_numeric($room_id)) return 0;
        $roomwaits = (array)$this->redis->lRange('roomwaits_' . $room_id);
        $count = count($roomwaits);
        //$strWhere = "room_id=".$room_id;
        //$count = M('game_roomwait')->where($strWhere)->order("id asc")->count();
        //$doing = M('game_history')->where('room_id='.$room_id.' and status<2')->count();//正在玩的
        $doing = $this->redis->get('roomgame_' . $room_id);
		$gamelock = $this->redis->get('gamelock_' . $room_id);
		if($gamelock && time() >= $gamelock['time']){
			$this->redis->delete('gamelock_' . $room_id);
			$gamelock = null;
		}
		if($gamelock['user_id'] == $this->user_id)return 1;//如果是自己
        $doing = $gamelock || $doing ? 1 : 0;
        if (!$count) {
            if ($doing) {
                //$strWhere = 'room_id='.$room_id.' and user_id='.$this->user_id;
                //$data = M('game_roomwait')->field('*')->where($strWhere)->find();
                //if(!$data['id'])return 0;//还没预约
                if (!in_array($this->user_id, $roomwaits)) {
                    return 0;
                }
            }
            return 1 + $doing;//没有人排队
        }
        //$strWhere = 'room_id='.$room_id.' and user_id='.$this->user_id;
        //$data = M('game_roomwait')->field('*')->where($strWhere)->find();
        //if(!$data['id'])return 0;//还没预约
        if (!in_array($this->user_id, $roomwaits)) {
            return 0;
        }
        //$strWhere = "room_id=".$room_id." and id<=".$data['id'];
        //$count = M('game_roomwait')->where($strWhere)->order("id asc")->count();
        $count = 1;
        foreach ($roomwaits as $v) {
            if ($v == $this->user_id) break;
            $count++;
        }
        return $doing ? $count + $doing : $count;//预约位置
    }

    private function getGl($type)
    {
        ;
        //0 free 1 normal


            if ($grade = UsersController::getGrade($this->user))
            {
                Log::record($this->user['user_id'].'xxxxxxxxxx grade : ' .  json_encode($grade),Log::INFO);
                if ($type == 1)
                {
                    return $grade['coin_strong_num'];
                }
                else{
                    return $grade['free_coin_strong_num'];
                }
            }

    }
    // 开始游戏
    private function start($room_id, $game_history_id)
    {
        // 根据房间号查询设备号
        $arrFac = M('game_room')->field('fac_id,is_sellmodel,is_roommodel,claw_count')->where('id=' . $room_id)->find();
        //计算强抓力
        $is_strongdec = 0;//用户要减强抓力
        $is_strong = 0;
        $sellmodel = M('sellmodel')->field('*')->order("money asc")->select();
		//最近的一次充值
	$pay_record = M('pay_record')->where("status=1 and user_id=".$this->user_id)->order("id desc")->find();
	$strongrand = 0;//送几次强抓
	foreach($sellmodel as $v){
		if($pay_record['money'] >= $v['money']){
			$strongrand = max($strongrand, $v['count']);
		}
	}
		
	$is_roommodel = 0;
        if ($arrFac['claw_count'] && $arrFac['is_roommodel']) {//抓几次出一次强抓力
            $nnum = $arrFac['claw_count'];
            Log::record($this->user['user_id'].'xxxxxxxxxx free gl base : ' . $nnum,Log::INFO);
            //如果用户单独设置了
            if ($this->user['free_coin']>0)
            {
                if ($this->user['free_coin_strong_num'] != 0)
                {
                    $nnum = $nnum+intval($nnum*user['free_coin_strong_num']/100);
                   Log::record($this->user['user_id'].'xxxxxxxxxx free gl num : ' .  $this->user['free_coin_strong_num'],Log::INFO);
                }
                else{
                    if ($arrFac['is_roomgrademodel'])
                    {
                        $gl = $this->getGl(1);
                        Log::record($this->user['user_id'].'xxxxxxxxxx free gl : ' . $gl,Log::INFO);
                        $nnum = $nnum+intval($nnum*$gl/100);
                    }else{
                        Log::record($this->user['user_id'].'xxxxxxxxxx free  not gl : ' ,Log::INFO);
                    }

                }

            }
            else{
                if ($this->user['coin_strong_num'] != 0)
                {
                    $nnum = $nnum+intval($nnum*user['coin_strong_num']/100);
                   Log::record($this->user['user_id'].'xxxxxxxxxx  gl num : ' . $this->user['coin_strong_num'],Log::INFO);
                }
                else{
                    if ($arrFac['is_roomgrademodel'])
                    {
                        $gl = $this->getGl(0);
                        $nnum = $nnum+intval($nnum*$gl/100);
                        Log::record($this->user['user_id'].'xxxxxxxxxx  gl  : ' . $nnum,Log::INFO);
                    }
                    else{
                        Log::record($this->user['user_id'].'xxxxxxxxxx  not gl  : ' ,Log::INFO);
                    }

                }

            }
           Log::record($this->user['user_id'].'xxxxxxxxxx nnum: ' . $nnum,Log::INFO);
            $historys = M('game_history')->field('is_strong,success')->where('room_id=' . $room_id)->order('id desc')->limit($nnum)->select();
            $flag     = $historys ? 1 : 0;
            $sucflag  = $historys ? 1 : 0;//1 出强力  0 不出
            foreach ($historys as $v) {
                if ($v['is_strong']) {
                    $flag = 0;
                }
                if ($v['success']) {
                    $sucflag = 0;
                }
            }
            $is_strong    = $flag ? $flag : $sucflag;//出现过strong 不再出  没有出现过  已经成功过不再出  否则出
            $is_strongdec = 0;
	    $is_roommodel = $is_strong;
        }
	$is_sellmodel = 0;
        if (!$is_strong && $this->user['strong'] && $strongrand && $arrFac['is_sellmodel']) {
            $rand = $this->redis->get('userStrongRand_' . $this->user_id);
            if (!$rand) {
                $rand = rand(max(1, intval($strongrand / 1.5)), $strongrand);
                $this->redis->set('userStrongRand_' . $this->user_id, $rand);
            }
	    $count = M('game_history')->where('ctime>='.$pay_record['paytime'].' and user_id=' . $this->user_id . ' and room_id=' . $room_id)->count();
	    $page = ceil($count / $strongrand);
	    $last = $count % $strongrand;
	    if(!$last)$last = $strongrand;
	    $limit = $last + $strongrand;
            $historys = (array)M('game_history')->field('is_strong,success')->where('ctime>='.$pay_record['paytime'].' and user_id=' . $this->user_id . ' and room_id=' . $room_id)->order('id desc')->limit($limit)->select();
            $flag     = $count && $last >= $rand ? 1 : 0;
            $sucflag  = $count && $last >= $rand ? 1 : 0;
	    if($page > 1){//上一页未中奖则直接是强抓力
	    	$prevflag = 1;
		foreach($historys as $k => $v){
			if($k >= $last){
				if($v['success'])$prevflag = 0;
			}
		}
		if($prevflag){
			$flag = 1;
			$sucflag = 1;
		}
	    }
            foreach ($historys as $k => $v) {
	    	if($k < $last){
	                if ($v['is_strong']) {
	                    $flag = 0;
	                }
	                if ($v['success']) {
	                    $sucflag = 0;
	                }
		}
            }
            $is_strong    = $flag ? $flag : $sucflag;
            $is_strongdec = $flag;
	    $is_sellmodel = $is_strong;
	    $this->redis->set('userStrongDec_'.$this->user_id, 1);//$is_strongdec);		
        }        

        // 读取设备信息
        $arrFacInfo = M('device as a')->join('cmf_game_config as c on a.game_config_id=c.id', 'left')->field('c.*')->where('a.device_unique_code="' . $arrFac['fac_id'] . '"')->find();

        $params = array($arrFacInfo['qh_speed'],
            $arrFacInfo['zy_speed'],
            $arrFacInfo['sx_speed'],
            $arrFacInfo['bzj_second_zhuali'],
            $arrFacInfo['bzj_first_zhuali'],
            $arrFacInfo['bzj_top'],
            30, 0);
        if ($is_strong) { // 中奖
            $params[3] = $arrFacInfo['zj_second_zhuali'];
            $params[4] = $arrFacInfo['zj_first_zhuali'];
            $params[5] = $arrFacInfo['zj_top'];
        }

        //array($arrFacInfo['qh_speed'], $arrFacInfo['zy_speed'], $arrFacInfo['sx_speed'], $arrFacInfo['zj_second_zhuali'], $arrFacInfo['zj_first_zhuali'], $win, $arrFacInfo['game_time'], 0),

        $str = $this->_game_start($arrFac['fac_id'],$game_history_id,$params);
		$this->log_message($str);
        $arr = json_decode($str, true);
        if ($arr['code'] == 1 && is_numeric($arr['data']) && $arr['data'] == 0) {
            //强抓力减1
            //if ($is_strongdec) M("users")->where(['id' => $this->user_id])->setDec("strong", 1);
			//激光
			$ret=$this->_set_light($arrFac['fac_id'],2,330,0,1);
			
            return $is_strong ? ($is_roommodel ? 1 : 2) : 3;
        } else {
            return 0;
        }
    }

    // 基本天车操作
    private function move($room_id, $game_history_id, $cmd_type, $is_strong)
    {
        // 根据房间号查询设备号
        $arrFac = M('game_room')->field('fac_id')->where('id=' . $room_id)->find();
        $arrFacInfo = M('device as a')->join('cmf_game_config as c on a.game_config_id=c.id', 'left')->field('c.*')->where('a.device_unique_code="' . $arrFac['fac_id'] . '"')->find();

        // 操作数值
        $goParams = array();
        switch ($cmd_type) {
            case 'up' : // 向前
                $goParams = array($arrFacInfo['qh_time'], 0, 0, 0, 0, $is_strong ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'], 0, 0);
                break;
            case 'down' : // 向后
                $goParams = array(-1 * $arrFacInfo['qh_time'], 0, 0, 0, 0, $is_strong ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'], 0, 0);
                break;
            case 'left' : // 向左
                $goParams = array(0, -1 * $arrFacInfo['zy_time'], 0, 0, 0, $is_strong ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'], 0, 0);
                break;
            case 'right' : // 向右
                $goParams = array(0, $arrFacInfo['zy_time'], 0, 0, 0, $is_strong ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'], 0, 0);
                break;
            case 'grab' : // 下爪
                $goParams = array(0, 0, 1, 0, 0, $is_strong ? $arrFacInfo['zj_top_time'] : $arrFacInfo['bzj_top_time'], 0, 0);
                break;
        }




        $str = $this->_paw_move($arrFac['fac_id'],$game_history_id,$goParams);
        $arr = json_decode($str, true);
        if ($arr['code'] == 1 && is_numeric($arr['data']) && $arr['data'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    // 甩抓
    public function throw_paw($room_id, $game_history_id, $is_strong)
    {
        $arrPrams = array(
            0 => array(2, 0, 0, 0, 0, 0, 0, 0), // 前
            1 => array(0, 2, 0, 0, 0, 0, 0, 0), // 右
            2 => array(-2, 0, 0, 0, 0, 0, 0, 0), // 后
            3 => array(0, -2, 0, 0, 0, 0, 0, 0) // 左
        );

        // 根据房间号查询设备号
        $arrFac = M('game_room')->field('fac_id')->where('id=' . $room_id)->find();
        $str = $this->_throw_paw($arrFac['fac_id'],$game_history_id,$arrPrams);

        $arr = json_decode($str, true);
        //$this->log_message($arr);
        //if($arr['code'] == 1 && is_numeric($arr['data']) && $arr['data'] == 0){
        if ($arr['code'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    // 抓娃娃的返回结果
    public function success()
    {
		//$this->log_message($_POST);
		
        $macno = I('macno'); // 设备唯一码
        $sysnum = I('sysnum'); // 系统流水
        $gift = intval(I('gift')); // 大于0为抓取成功,为抓取数量
        $status = intval(I('status')); //状态0空闲，1游戏中
        /* 设备号 */
        $mac = M('device')->where(['device_unique_code'=>$macno])->getField('deveci_no');
        M('device')->where(['device_unique_code' => $macno])->save(['beat_time' => time(),"status"=>1]);
        $roomInfo = M('game_room')->field("id as room_id,room_no,room_name,fac_id,type_id,wawa_num,yj_kc")->where('fac_id="' . $macno . '"')->find();
        if (!$roomInfo)  exit('1');
		
		//心跳
		M('game_room')->where(['id' => $roomInfo['room_id']])->save(['beat_time' => time()]);
		
        //记录故障
        //机器运行状态0：空闲，1：正在使用241：爪子上升超时。242：天车回原位超时243：前后电机电流异常244：左右电机电流异常245：上下电机电流异常246：爪子线圈电流异常247：光眼异常248：保留，无意义249：参数不合法
        $errornos = array(
            241 => 'F1  爪子上升超时',
            242 => 'F2  天车回原位超时',
            243 => 'F3  前后电机电流异常',
            244 => 'F4  左右电机电流异常',
            245 => 'F5  上下电机电流异常',
            246 => 'F6  爪子线圈电流异常',
            247 => 'F7  光眼异常',
            248 => 'F8  保留，无意义',
            249 => 'F9  参数不合法'
        );
        if (in_array($status, array_keys($errornos))) {
            $count = M('fault')->where(['error_no' => $status, 'device_no' => $macno, 'ctime' => ['egt', time() - 30]])->count();
            if (!$count) {
                $fault = array(
                    'error_no' => $status,
                    'device_no' => $macno,
                    'room_id' => $roomInfo['room_id'],
                    'room_no' => $roomInfo['room_no'],
                    'room_name' => $roomInfo['room_name'],
                    'content' => $errornos[$status],
                    'ctime' => time()
                );
                M('fault')->add($fault);
                //发短信
                $phone = M('config')->where(['id' => 1])->getField('phone');
                $content = "<设备故障>房间：" . $roomInfo['room_name'] . "，设备：" . $mac . "，故障：" . $errornos[$status];
                sendSMS($phone, $content);
            }
            M('device')->where(['device_unique_code' => $macno])->save(['status' => 0]);
			$room_where = 'status not in (1,2) and id='.$roomInfo['room_id'];
			$room_status = 2;
            $room_ids = M('game_room')->where($room_where)->getField('id', true);
            M('game_room')->where($room_where)->save(['status' => 2]);
        } else {
            M('device')->where(['device_unique_code' => $macno])->save(['status' => 1]);
			$room_where = 'status in (0,2,4) and id='.$roomInfo['room_id'];
			$room_status = 0;
            $room_ids = M('game_room')->where($room_where)->getField('id', true);
            M('game_room')->where($room_where)->save(['status' => 0]);
        }
        if($room_ids)$this->notice_gameover('0', '{"type":15,"room_ids":"'.implode(',', $room_ids).'","status":'.$room_status.'}');
		
        if ($status) exit('2');
        $roomgame = $this->redis->get('roomgame_' . $roomInfo['room_id']);
        if (!$roomgame) {
            exit('3');
        }
        if (time() - $roomgame['ctime'] < 5) exit('');//防止直接结束
		if($roomgame['ctime'] != $sysnum)exit('');//不是本局游戏
		$gameUser = $this->redis->get('gameuser_' . $roomInfo['room_id']);

        // 获取娃娃类型id
        //$gameInfo = M('game_history')->alias('a')->join('cmf_game_room as b on a.room_id=b.id')->field('a.id,a.room_id,a.status, a.user_id,a.ctime, a.usetime, a.gametime, b.fac_id, b.type_id')->where('b.fac_id="'.$macno.'" and a.ctime='.$sysnum)->find();
        //$gameInfo = M('game_history')->field('id,status,user_id,ctime,usetime,gametime')->order('id desc')->where('room_id="'.$roomInfo['room_id'].'" and a.status<2')->find();
        //if(!$gameInfo){
        //	exit('');
        //}
        $gameInfo = array_merge(
            array(
                'id' => $roomgame['id'],
                'status' => $roomgame['status'],
                'user_id' => $roomgame['user_id'],
                'ctime' => $roomgame['ctime'],
                'usetime' => $roomgame['usetime'],
                'gametime' => $roomgame['gametime'],
            ),
            $roomInfo);
        //$this->log_message($gameInfo);

        //如果没有人在玩游戏//APP每次轮询清掉超过5秒未开始的排队第一位//并发消息给下一位
        //if($status == 0){
        /*$roomwaits = (array)$this->redis->lRange('roomwaits_'.$gameInfo['room_id']);
        $temp = array();
        foreach($roomwaits as $v){
                $mywait = $this->redis->get('mywait_'.$v);
                if($mywait['tim_time'] && time() - $mywait['tim_time'] > 5){//判断删除超时预约
                    $this->redis->lRemove('roomwaits_'.$mywait['room_id'], $v);
                    $this->redis->delete('mywait_'.$v);
                    continue;
                }
                $temp[] = $mywait;
        }

        if(!$temp[0]['tim_time'] && $temp[0]['tim_uid']){
            include_once THINK_PATH.'../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
            $api = \createRestAPI();
            $api->openim_send_msg(false,$temp[0]['tim_uid'],'{"type":11,"room_id":'.$gameInfo['room_id'].', "timestamp": "'.microtime().'"}');
            $temp[0]['tim_time'] = time();
            $this->redis->set('mywait_'.$temp[0]['user_id'], $temp[0]);//更新
        }*/

        /*$waitlist = M('game_roomwait')->where("room_id=".$gameInfo['room_id'])->limit(2)->order('id asc')->select();
        if($waitlist[0]){
            if($waitlist[0]['tim_time'] && (time() - $waitlist[0]['tim_time']) > 5){
                M('game_roomwait')->where(['id' => $waitlist[0]['id']])->delete();
            }else{
                $waitlist[1] = $waitlist[0];
            }
        }
        //发送IM消息给下一个排队
        if($waitlist[1]['tim_uid'] && !$waitlist[1]['tim_time']){
            include_once THINK_PATH.'../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
            $api = \createRestAPI();
            $api->openim_send_msg(false,$waitlist[1]['tim_uid'],'{"type":11,"room_id":'.$gameInfo['room_id'].', "timestamp": "'.microtime().'"}');
            M('game_roomwait')->where('id='.$waitlist[1]['id'])->save(['tim_time' => time()]);
        }*/
        //}

        //if($gameInfo['status'] ==2||$status!=0) die();//

        $giftdata = array();
        // 插入我的娃娃表
        if ($gift) {
			$gift = 1;
            $arr = array();
            $arr['wawa_id'] = $gameInfo['type_id'];
            $arr['user_id'] = $gameInfo['user_id'];
            $arr['ctime'] = time();
            $return_id = M('user_wawas')->add($arr);
            $user_nicename = M('users')->where('id=' . $gameInfo['user_id'])->getField('user_nicename');
            $giftname = M('gift')->where('id=' . $gameInfo['type_id'])->getField('giftname');
//            $title = $user_nicename . '抓到了1个' . $giftname;
            $title = '恭喜！';
            $desc = '抓中了！';
            $content = '你抓到了1个' . $giftname;;
            $notice_data = $this->notice_add(1, $gameInfo['user_id'], $title,0,$desc,$content);
            $this->notice_gameover('0', json_encode(array("type" => 12, "new_notice" => $notice_data, "timestamp" => microtime())));//推送消息

            $update['total_get'] = array('exp', 'total_get+'.(M('gift')->where(['id'=>$arr['wawa_id']])->getField('cost')));
            M('users')->where("id={$gameInfo['user_id']}")->save($update);

			//减库存
            M("game_room")->where(['id' => $gameInfo['room_id']])->setDec("wawa_num", $gift);
			//M("gift")->where(['id' => $gameInfo['type_id']])->setDec("stock", $gift);
            //库存预警
            if ($gameInfo['wawa_num'] - $gift <= $gameInfo['yj_kc']) {
                /* 发送短信 */
                $phone = M('config')->where(['id' => 1])->getField('phone');
//                $content = "【库存预警】房间：" . $roomInfo['room_name'] . "，剩余库存：" . ($gameInfo['wawa_num'] - $gift) . "，剩余库存不足，请及时补货！";
                $content = "【库存不足】房间：" . $roomInfo['room_name'] . "设备编号：" . $mac . "，库存：" . ($gameInfo['wawa_num'] - $gift);
                sendSMS($phone, $content);
				//补货
				if($gameInfo['wawa_num'] - $gift <= 0){
					M('game_room')->where(['id' => $gameInfo['room_id']])->save(['status' => 1]);
					$this->notice_gameover('0', '{"type":15,"room_ids":"'.$gameInfo['room_id'].'","status":1}');
				}
            }
            //消费记录标志
            M('users_coinrecord')->where(['action' => "zhuawawa", 'showid' => $gameInfo['id']])->save(array('giftcount' => $gift));
            $giftdata['count_success'] = array('exp', 'count_success+1');
			
			$this->redis->delete('userStrongRand_'.$gameInfo['user_id']);
			$is_strongdec = $this->redis->get('userStrongDec_'.$gameInfo['user_id']);
			$this->redis->delete('userStrongDec_'.$gameInfo['user_id']);			
			if($is_strongdec)M("users")->where(['strong'=>['egt', 1],'id' => $gameInfo['user_id']])->setDec("strong", 1);
        }
        $giftdata['count'] = array('exp', 'count+1');
        M('gift')->where(['id' => $gameInfo['type_id']])->save($giftdata);

        $arrSet = array();
        $arrSet['success'] = intval($return_id);
        $arrSet['usetime'] = time() - $gameInfo['ctime'];
		//$arrSet['is_strong'] = $gift ? 1 : 0;
        $arrSet['status'] = 2;
        $arrSet['totals'] = $gift;
        M('game_history')->where('id=' . $gameInfo['id'])->save($arrSet);
        M('game_room')->where('id=' . $gameInfo['room_id'])->save(array('status' => 0));
		$this->notice_gameover('0', '{"type":15,"room_ids":"'.$gameInfo['room_id'].'","status":0}');
        //计算送甩抓
        $config = M('config')->field('claw_count')->find();
        if ($config['claw_count']) {
			$claw_give_time = intval(M('users')->where("id=" . $gameInfo['user_id'])->getField('claw_give_time'));
            $count = M('game_history')->where('user_id=' . $gameInfo['user_id'] . ' and ctime>=' . $claw_give_time)->count();
            if ($count >= $config['claw_count']) {
                M('users')->where("id=" . $gameInfo['user_id'])->save(array("claw" => array('exp', 'claw+1'), "claw_give_time" => time()));
            }
        }
        //$b = microtime();
        //发通知给下一个人
        /*
		$roomwaits = (array)$this->redis->lRange('roomwaits_' . $gameInfo['room_id'], 0, 0);
        foreach ($roomwaits as $v) {
            $mywait = $this->redis->get('mywait_' . $v);
            if (!$mywait['tim_time'] && $mywait['tim_uid']) {
                $ret1 = $this->notice_gameover('0', '{"type":11,"user_id":' . $v . ',"room_id":' . $gameInfo['room_id'] . ', "timestamp": "' . microtime() . '"}');
                //$this->log_message(array('ret1' => $ret1));
                $mywait['tim_time'] = time();
                $this->redis->set('mywait_' . $v, $mywait);//更新
            }
            break;
        }*/
		//锁定5秒
		$this->redis->set('gamelock_'.$gameInfo['room_id'], ['user_id' => $gameInfo['user_id'], 'time' => time() + 8]);
        //$e1 = microtime();
        $ret2 = $this->notice_gameover($gameInfo['room_id'], json_encode(array("success" => $gift, "user_id" => $gameInfo['user_id'], "user_nicename" => $gameUser['user_nicename'], "game_id" => $gameInfo['id'], "room_id" => $gameInfo['room_id'], "type" => 10)));

        //$e2 = microtime();
        //$this->log_message(array($b, $e1, $e2));
        //$this->log_message(array('ret2' => $ret2));
		
        $this->redis->delete('roomgame_' . $roomInfo['room_id']);
        $this->redis->delete('gameuser_' . $roomInfo['room_id']);
		
		exit('4');
    }

    private function notice_gameover($room_id, $msg)
    {
        include_once THINK_PATH . '../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
        $api = \createRestAPI();
        return $api->group_send_group_msg(false, strval($room_id), $msg);
    }

    // 查询是否抓取成功
    public function check_result()
    {
        $id = intval(I('game_history_id')); // 记录id
        if (empty($id)) {
            $this->_return(-1001, '记录id不能为空');
        }
        $row = M('game_history')->field('totals')->where('id=' . $id)->find();
        return $row['totals']; // 抓取的数量
    }

    // 用户退出房间
    public function exit_room()
    {
        $room_id = intval(I('room_id'));
        M('game_audience')->where('user_id=' . $this->user_id)->delete();
        //if($room_id)M('game_roomwait')->where('room_id='.$room_id.' and user_id='.$this->user_id)->delete();
        //if($room_id)$this->redis->lRemove('roomwaits_'.$room_id, $this->user_id);
        $this->_return(1, '退出房间成功');
    }

    /**
     * 获取人工服务开关
     */
    public function get_service_switch()
    {
        $res = M('config')->where(['id' => 1])->getField('game_service');
        $this->_return(1, '获取成功', $res);
    }

    /**
     * 分享视频
     */
    public function game_link(){
        $game_id = I('game_id',0,'intval');
        if( !$game_id )$this->_return(-1,'id不存在');
        $url = M('game_history')->where(['id'=>$game_id])->getField('video');
        $this->_return(1,'获取成功',$url);
    }

    public function _set_light($macno,$type,$ontime,$offtime,$period,$seconds=0){
        $ret["code"]=0;
        $ret['data']="";
        if(empty($macno)||empty($ontime)){
            $ret["msg"]="缺少参数";
        }else{
            $type=$type==2?2:1;
            $redis_beat=$this->redis->get($macno.":COM");
            $macinfo=$redis_beat;
            if($macinfo['lastbeat']+30<time()) return json_encode(array("code"=>-1,"msg"=>"设备不在线","data"=>""));
            if(!empty($macinfo)){
                $command="3AA300000001".$macno."00080AD00".$type.substr("000".dechex($ontime),-4).substr("000".dechex($offtime),-4).substr('0'.dechex($period),-2);
                $sendcom=hex2bin($command.$this->getcrc16($command));
                if($this->send($macinfo['ip'],$macinfo['port'],$sendcom)){
                    $ret["code"]=1;
                    $ret["msg"]="发送成功";
                    if(!$seconds) $seconds=3;
                    $seconds=$seconds*10;
                    $sendtime=time();
                    while($seconds/10>0){
                        usleep(100000);
                        $redis_ret=$this->redis->get($macno.":0AD0");
                        if(!empty($redis_ret)&&$redis_ret['ctime']>=$sendtime){
                            $ret['data'] = $redis_ret['data'];
                            $this->redis->delete($macno.":0AD0");
                            break;
                        }
                        $seconds--;
                    }
                }else{
                    $ret["msg"]="发送失败";
                }
            }else{
                $ret["msg"]="该设备不存在";
            }
        }
        return json_encode($ret);
    }
    private function _game_start($macno,$sysnum,$params,$seconds=0){
        $ret["code"]=0;
        $ret['data']="";
        if(empty($macno)||empty($sysnum)||empty($params)){
            $ret["msg"]="缺少参数";
        }else{
            $redis_beat=$this->redis->get($macno.":COM");
            $macinfo=$redis_beat;
			$this->log_message($macinfo);
            if($macinfo['lastbeat']+30<time()) return json_encode(array("code"=>-1,"msg"=>"设备不在线","data"=>""));
            if(!empty($macinfo)){
                $paramstr=$this->_scratch_param($params,"FF55C1");
                $command="3AA300000001".$macno."00120AD1".substr("0000000".dechex($sysnum),-8).$paramstr;
                $sendcom=hex2bin($command.$this->getcrc16($command));
                $this->redis->set($macno.":0AD1Start",time());
                if($this->send($macinfo['ip'],$macinfo['port'],$sendcom)){
                    $ret["code"]=1;
                    $ret["msg"]="发送成功";
                    if(!$seconds) $seconds=3;
                    $seconds=$seconds*3;
                    $sendtime=time();
                    while($seconds>0){
                        usleep(100000);
                        $redis_ret=$this->redis->get($macno.":0AD1");
                        if(!empty($redis_ret)){
                            $ret['data'] = $redis_ret['data'];
                            $this->redis->delete($macno.":0AD1");
                            break;
                        }
                        $seconds--;
                    }
                }else{
                    $ret["msg"]="发送失败";
                }
            }else{
                $ret["msg"]="该设备不存在";
            }
        }
        return json_encode($ret);
    }
    private function _paw_move($macno,$sysnum,$params,$seconds=0){
        $ret["code"]=0;
        $ret['data']="";
        if(empty($macno)||empty($sysnum)||empty($params)){
            $ret["msg"]="缺少参数";
        }else{
            $redis_beat=$this->redis->get($macno.":COM");
            $macinfo=$redis_beat;
            if($macinfo['lastbeat']+30<time()) return json_encode(array("code"=>-1,"msg"=>"设备不在线","data"=>""));
            if(!empty($macinfo)){
                $command="3AA300000001".$macno."00120AD2".substr("0000000".dechex($sysnum),-8).$this->_scratch_param($params);
                $sendcom=hex2bin($command.$this->getcrc16($command));
                if($this->send($macinfo['ip'],$macinfo['port'],$sendcom)){
                    $ret["code"]=1;
                    $ret["msg"]="发送成功";
                    if(!$seconds) $seconds=3;
                    $seconds=$seconds*10;
                    $sendtime=time();
                    while($seconds/10>0){
                        usleep(100000);
                        $redis_ret=$this->redis->get($macno.":0AD2");
                        if(!empty($redis_ret)&&$redis_ret['ctime']>=$sendtime){
                            $ret['data'] = $redis_ret['data'];
                            $this->redis->delete($macno.":0AD2");
                            break;
                        }
                        $seconds--;
                    }
                }else{
                    $ret["msg"]="发送失败";
                }
            }else{
                $ret["msg"]="该设备不存在";
            }
        }
        return json_encode($ret);
    }
    public function _throw_paw($macno,$sysnum,$params,$seconds=0){
        $ret["code"]=0;
        $ret['data']=array();
        if(empty($macno)||empty($sysnum)||empty($params)){
            $ret["msg"]="缺少参数";
        }else{
            $redis_beat=$this->redis->get($macno.":COM");
            $macinfo=$redis_beat;
            if($macinfo['lastbeat']+30<time()) return json_encode(array("code"=>-1,"msg"=>"设备不在线","data"=>""));
            if(!empty($macinfo)){
                $command="3AA300000001".$macno."003A0AD3".substr("0000000".dechex($sysnum),-8)."0C";
                foreach($params as $k=>$v){
                    $command.=$this->_scratch_param($v);
                    if($k<count($params)-1){
                        $runtime=abs($v[0]+$v[1]);
                        // if($runtime>15)$runtime=256-$runtime;
                        $command.=substr('0'.dechex($runtime),-2);
                    }
                }
                $sendcom=hex2bin($command.$this->getcrc16($command));
                if($this->send($macinfo['ip'],$macinfo['port'],$sendcom)){
                    $ret["code"]=1;
                    $ret["msg"]="发送成功";
                    if(!$seconds) $seconds=3;
                    $sendtime=time();
                    while($seconds/10>0){
                        usleep(100000);
                        $redis_ret=$this->redis->get($macno.":0AD3");
                        if(!empty($redis_ret)&&$redis_ret['ctime']>=$sendtime){
                            $ret['data'] = $redis_ret['data'];
                            $this->redis->delete($macno.":0AD3");
                            break;
                        }
                        $seconds--;
                    }
                }else{
                    $ret["msg"]="发送失败";
                }
            }else{
                $ret["msg"]="该设备不存在";
            }
        }
        return json_encode($ret);
    }
    private function _scratch_param($param,$first="FF55C2"){
        $str=$first;
        foreach ($param as $v)
        {
            $key = substr('0'.dechex($v),-2);
            $str .= substr($key,- 2);
        }
        $str .= $this->_get_addstr(hex2bin($str));
        return $str;
    }
    private function _get_addstr($data){
        if (strlen($data) < 2) return "00";
        $lastkey = hexdec(substr('0'.dechex(ord($data[0]) + ord($data[1])),-2));
        for ($i = 2; $i < strlen($data); $i++){
            $lastkey = hexdec(substr('0'.dechex($lastkey + ord($data[$i])),-2));
        }
        return strtoupper(substr('0'.dechex($lastkey),-2));
    }
    private function getcrc16($Source){
        $crc = 0xA1EC;          // initial value
        $polynomial = 0x1021;   // 0001 0000 0010 0001  (0, 5, 12) 
        $tmp = "";
        $bytes = array();
        for ($i = 0; $i < strlen($Source) - 1; $i++)
        {
            if ($i % 2 == 0)
            {
                $tmp = substr($Source,$i, 2);
                $bytes[$i / 2] = hexdec($tmp);
            }
        }
        foreach ($bytes as $b)
        {
            for ($i = 0; $i < 8; $i++)
            {
                $bit = (($b >> (7 - $i) & 1) == 1);
                $c15 = (($crc >> 15 & 1) == 1);
                $crc <<= 1;
                if ($c15 ^ $bit) $crc ^= $polynomial;
            }
        }
        $crc &= 0xffff;
        $strDest = $crc;
        return strtoupper(substr("000".dechex($strDest),-4));
    }
    private function send($ip,$port,$command){
        $client = new \swoole_client(SWOOLE_SOCK_UDP);
        $client->set(array(
                'bind_address'  =>  '0.0.0.0',
                'bind_port'     =>  7518,
        ));
        if (!$client->connect($ip, $port, 0.5))
        {
            return false;
        }else{
            if (!$client->send($command))
            {
                return false;
            }else{
                $client->close();
                return true;
            }
        }
    }
}
