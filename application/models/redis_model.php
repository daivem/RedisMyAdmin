<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redis_Model extends CI_Model {
	
	private $_redis;
	
	private $_host;
	private $_port;
	private $_db;
	private $_auth;

	private $_config;	

	private $_is_cluster;
	
	public function __construct()
	{
		parent::__construct();
				
	}
	
	public function get_redis_instance()
	{
		return $this -> _redis;
	}
	
	public function init($redis_config)
	{
	
		if ( ( empty($redis_config) )
			|| ( ! is_array($redis_config) )
		){
			show_error('Redis Config Error');
		}
	
		$this -> _is_cluster = FALSE;

		if ( isset($redis_config['host']) ) {
			return $this -> _init_redis($redis_config);
		} elseif ( ( isset($redis_config['cluster_list']) )
				&& ( is_array($redis_config['cluster_list']) )
		){
			$this -> _init_cluster($redis_config);
			$this -> _is_cluster = TRUE;
		} else{
			show_error('Redis Config Error');
		}
		
		$this -> _config = $redis_config;	
	}
	
	private function _init_cluster($redis_config)
	{
		$cluster_list = $redis_config['cluster_list'];
		try{
			$this -> _redis = new RedisCluster(
					NULL, $cluster_list
			);
		} catch (Exception $e) {
			show_error('Can not connect to Redis Cluster. Message:' . $e -> getMessage());
		}
		
		if ( ! $this -> auth() ) {
			show_error('Redis Server (' . $this -> _host . ':' . $this -> _port . ') 认证密码错误!');
		}
	}
	
	
	
	private function _init_redis($redis_config)
	{

		$this -> _host = $redis_config['host'];
		$this -> _port = isset($redis_config['port']) ? $redis_config['port'] : 6379;
		$this -> _db = isset($redis_config['db']) ? $redis_config['db'] : 0;
		$this -> _auth = isset($redis_config['auth']) ? $redis_config['auth'] : FALSE;
		
		
		$this -> _redis = new Redis();
		
		$conn_success = FALSE;
		try {
			$conn_success = $this -> _redis -> connect($this -> _host, $this -> _port);
		} catch (Exception $e) {
			$conn_success = FALSE;
		}
		
		if ( ! $conn_success ) {
			show_error('Can not connect to Redis Server (' . $this -> _host . ':' . $this -> _port . ')');
		}
		if ( ! $this -> auth() ) {
			show_error('Redis Server (' . $this -> _host . ':' . $this -> _port . ') 认证密码错误!');
		} 		
		
		$this -> select_db($this -> _db);
	}
	
	public function get_db_size()
	{
		if (CLUSTER_MODE) {
			$redis = $this -> get_redis_instance();
			$db_size = 0;
			foreach($redis -> _masters() as $s) {
				$db_size += $redis -> dbsize($s);
			}
		} else {
			$db_size = $this -> get_keys_count();
		}
		return $db_size;
	}
	
	/**
	 * 
	 * 选择数据库
	 * @param unknown_type $db
	 */
	public function select_db($db)
	{
		$db = (int)$db;
		$this -> _db = $db;
		try {
			$this -> _redis -> select($this -> _db);
		} catch (Exception $e) {
			show_error('连接服务器时发生错误：' . $e -> getMessage());
		}
	}
	
	/**
	 * 设置认证密码
	 */
	public function auth()
	{
		if ( ( FALSE !== $this -> _auth )
			&& ( NULL !== $this -> _auth )
		){
			return $this -> _redis -> auth($this -> _auth);
		}
		return TRUE;
	}
	
	/**
	 * 
	 * 取得KEY类型
	 * @param unknown_type $key
	 */
	public function get_key_type($key)
	{
		return $this -> _redis -> type($key);
	}
	
	
	public function get_keys_count()
	{
		return $this -> _redis -> dbSize();
	}
	
	/**
	 * 
	 * 取得当前DB所有KEYS
	 * @param unknown_type $prefix
	 */
	public function get_all_keys($prefix = '', $need_sort = TRUE)
	{
		if ( ( method_exists($this -> _redis, 'scan') ) 
			&& ( ! $this -> _is_cluster )
		){
			return $this -> get_all_keys_by_scan($prefix, $need_sort);
		}

		if ( !empty($prefix) ) {
		    $keys = $this -> _redis -> keys($prefix . '*');
		} else {
		    $keys = $this -> _redis -> keys('*');
		}
	
		if ( $need_sort ) {
			sort($keys);		
		}
		return $keys;
	}

	public function get_all_keys_by_scan($prefix = '', $need_sort = TRUE)
	{
		$keys = array();
		$scan_prefix = empty($prefix) ? NULL : $prefix . '*';
		$this -> _redis -> setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);	
		$iterator = NULL;
		while ($arr_keys = $this -> _redis -> scan($it, $scan_prefix, 1000)) {
			$keys = array_merge($keys, $arr_keys);
		}

		if ( $need_sort ) {
			sort($keys);		
		}
		return $keys;
	}
	
	public function scan_keys($prefix, $iterator, $need_sort = TRUE, $page_size = TREE_KEY_PAGE_SIZE)
	{
		$keys = array();
		$this -> _redis -> setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);	
		if ( ! $this -> _is_cluster ) {
			$scan_prefix = empty($prefix) ? NULL : $prefix . '*';
			$keys = $this -> _redis -> scan($iterator, $scan_prefix, $page_size);
		} else {
			$scan_prefix = empty($prefix) ? '*' : $prefix . '*';
			$keys = $this -> _redis -> keys($scan_prefix);
			$iterator = 0;
		}

		if ( $need_sort ) {
			sort($keys);	
		}
		return array('iterator' => $iterator, 'keys' => $keys);

	}	
	
	public function get_redis_types() {
		// phpredis types to string conversion array.
		return array(
			Redis::REDIS_STRING => 'string',
			Redis::REDIS_SET => 'set',
			Redis::REDIS_LIST => 'list',
			Redis::REDIS_ZSET => 'zset',
			Redis::REDIS_HASH => 'hash',
		);
		
	}
	
}
