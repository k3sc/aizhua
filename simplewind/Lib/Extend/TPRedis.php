<?php
class TPRedis {

    protected $redis;

    protected $auth;

    protected $prefix;
	
	protected $format;

    /**
     * @param string $config['type']    Redis连接方式 unix,http
     * @param string $config['socket']  unix方式连接时，需要配置
     * @param string $config['host']    Redis域名
     * @param int    $config['port']    Redis端口,默认为6379
     * @param string $config['prefix']  Redis key prefix
     * @param string $config['auth']    Redis 身份验证
     * @param int    $config['db']      Redis库,默认0
     * @param int    $config['timeout'] 连接超时时间,单位秒,默认300
     */
    public function __construct($config) {
        $this->redis = new Redis();

        // 连接
        if (isset($config['type']) && $config['type'] == 'unix') {
            if (!isset($config['socket'])) {
               return false;
            }
            $this->redis->connect($config['socket']);
        } else {
            $port = isset($config['port']) ? intval($config['port']) : 6379;
            $timeout = isset($config['timeout']) ? intval($config['timeout']) : 300;
            $this->redis->connect($config['host'], $port, $timeout);
        }

        // 验证
        $this->auth = isset($config['auth']) ? $config['auth'] : '';
        if ($this->auth != '') {
            $this->redis->auth($this->auth);
        }

        // 选择
        $dbIndex = isset($config['db']) ? intval($config['db']) : 0;
        $this->redis->select($dbIndex);

        $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'thinkphp_';
		$this->format = $config['format'];
    }
	
	public function flushAll(){
		return $this->redis->flushAll();
	}

    /**
     * 将value 的值赋值给key,生存时间为expire秒
     */
    public function set($key, $value, $expire = 2592000) {
        $this->redis->setex($this->formatKey($key), $expire, $this->formatValue($value));
    }

    public function get($key) {
        $value = $this->redis->get($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function delete($key) {
		if(strpos($key, '*') !== false){
        	return $this->redis->delete($this->keys($key));
		}else{
        	return $this->redis->delete($this->formatKey($key));
		}
    }
	
	public function keys($key = '*', $all = false){
		if($all){
			$keys = $this->redis->keys($key);
		}else{
			$keys = $this->redis->keys($this->formatKey($key));
			foreach($keys as &$v){
				$v = $this->unformatKey($v);
			}
			unset($v);
		}
		return $keys;
	}

    /**
     * 检测是否存在key,若不存在则赋值value
     */
    public function setnx($key, $value) {
        return $this->redis->setnx($this->formatKey($key), $this->formatValue($value));
    }

    public function lPush($key, $value) {
        return $this->redis->lPush($this->formatKey($key), $this->formatValue($value));
    }

    public function rPush($key, $value) {
        return $this->redis->rPush($this->formatKey($key), $this->formatValue($value));
    }

    public function lPop($key) {
        $value = $this->redis->lPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function rPop($key) {
        $value = $this->redis->rPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }
	
	public function sIsMember($key, $value){
		return $this->redis->sIsMember($this->formatKey($key), $this->formatValue($value));
	}
	
	public function sContains($key, $value){
		return $this->redis->sContains($this->formatKey($key), $this->formatValue($value));
	}
	
	public function sRemove($key, $value){
		return $this->redis->sRemove($this->formatKey($key), $this->formatValue($value));
	}
	
	public function rPushx($key, $value){
        return $this->redis->rPushx($this->formatKey($key), $this->formatValue($value));
	}
	
	public function lRange($key, $begin = 0, $end = -1){
		$list = $this->redis->lRange($this->formatKey($key), $begin, $end);
		if($list === false)return NULL;
		foreach($list as &$v){
			$v = $this->unformatValue($v);
		}
		unset($v);
		return $list;
	}
	
	public function lSize($key){
		return $this->redis->lSize($this->formatKey($key));
	}
	
	public function lRemove($key, $value, $count = 0){
		return $this->redis->lRemove($this->formatKey($key), $this->formatValue($value), $count);
	}
	
	public function flushDB(){
		return $this->redis->flushDB();
	}

    protected function formatKey($key) {
        return $this->prefix . $key;
    }

    protected function formatValue($value) {
        return $this->format == 'json' ? @json_encode($value) : @serialize($value);
    }

    protected function unformatKey($key) {
        return substr($key, strlen($this->prefix));
    }

    protected function unformatValue($value) {
        return $this->format == 'json' ? @json_decode($value, true) : @unserialize($value);
    }
}
