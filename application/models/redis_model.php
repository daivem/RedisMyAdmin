<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redis_Model extends CI_Model {
	
	private $_redis;
	
	private $_host;
	private $_port;
	private $_db;
	
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
			|| ( ! isset($redis_config['host']) )
		){
			show_error('Redis Config Error');
		}
		
		$this -> _host = $redis_config['host'];
		$this -> _port = isset($redis_config['port']) ? $redis_config['port'] : 6379;
		$this -> _db = isset($redis_config['db']) ? $redis_config['db'] : 0;
		
		
		$this -> _redis = new Redis();
		
		$conn_success = FALSE;
		try {
	  		$conn_success = $this -> _redis -> connect($this -> _host, $this -> _port);
		} catch (Exception $e) {
			$conn_success = FALSE;
		}
		
		if ( ! $conn_success ) {
			die('Can not connect Redis Server (' . $this -> _host . ':' . $this -> _port . ')');
		}
		
		$this -> select_db($this -> _db);
		
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
		$this -> _redis -> select($this -> _db);
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
	public function get_all_keys($prefix = '')
	{
		if ( !empty($prefix) ) {
		    $keys = $this -> _redis -> keys($prefix . '*');
		} else {
		    $keys = $this -> _redis -> keys('*');
		}
		
		sort($keys);		
		return $keys;
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
