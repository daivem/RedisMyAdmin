<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 导出数据
 * @author DV
 *
 */
class Export extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ($this -> is_post()) {
			$this -> _do_index();
			return ;
		}	
		
		$type = get_arg('type', 'redis', 'trim');
		
		$key = get_arg('key', '', 'trim');

		$page_data = $this -> get_default_page_data();
		$page_data['type'] = $type;
		$page_data['key'] = $key;

		$page_data['title'] = '导出数据';
		$this -> load -> view('export', $page_data);
		
	}
	
	private function _do_index()
	{
		$type = get_arg('type', 'redis', 'trim');
		
		$key = get_arg('key', NULL);
		
		if ( $type == 'redis' ) {
			$ext = 'redis';
			$ct  = 'text/plain';
		} else {
			$ext = 'js';
			$ct  = 'application/json';
		}
		
		header('Content-type: '.$ct.'; charset=utf-8');
		header('Content-Disposition: inline; filename="export.'.$ext.'"');
			
		$redis = $this -> redis_model -> get_redis_instance();
		$key = ($key === NULL) ? '*' : $key;
		
		if ( $type == 'redis' ) {
			//导出Redis命令
			if ( substr($key, -1) == '*' ) {
				//导出所有KEY 或 部分KEY
				$keys = $redis -> keys($key);
				$values = array();
				foreach($keys as $k) {
					$values[] = $this -> _export_redis($k);
				}
				echo implode(PHP_EOL, $values);	
				
			} else {
				//导出单个KEY
				echo $this -> _export_redis($key);
			}
		
		} else {
			//导出json格式
			if ( substr($key, -1) == '*' ) {
				//导出所有KEY 或 部分KEY
				$keys = $redis -> keys($key);
				$values = array();
				foreach($keys as $k) {
					$values[$k] = $this -> _export_json($k);
				}
				echo json_encode($values);
			
			} else {
				//导出单个KEY
				echo json_encode( $this -> _export_json($key) );
			}
			
		}
		
		die();
	}
	
	

	/**
	 *
	 * 导出数据为 Redis命令行格式
	 * @param $key
	 */
	private function _export_redis($key)
	{
		$redis = $this -> redis_model -> get_redis_instance();
		$redis_types = $this -> redis_model -> get_redis_types();
	
		$type = $redis -> type($key);
		
		if ( ! isset($redis_types[$type]) ) {
			return;
		}
	
		$type = $redis_types[$type];
	
		// String
		if ($type == 'string') {
			return 'SET "' . addslashes($key) . '" "' . addslashes($redis -> get($key)) . '"';
		}
	
		// Hash
		else if ($type == 'hash') {
			$values = $redis -> hGetAll($key);
			$result = array();	
			foreach ($values as $k => $v) {
				$result[] = 'HSET "' . addslashes($key) . '" "' . addslashes($k) . '" "' . addslashes($v) . '"';
			}
			return implode(PHP_EOL, $result);
		}
	
		// List
		else if ($type == 'list') {
			$size = $redis -> lSize($key);
			$result = array();	
			for ($i = 0; $i < $size; $i++) {
				$result[] = 'RPUSH "' . addslashes($key) . '" "' . addslashes($redis -> lGet($key,  $i)) . '"';
			}
			return implode(PHP_EOL, $result);
		}
	
		// Set
		else if ($type == 'set') {
			$values = $redis -> sMembers($key);
				
			$result = array();	
			foreach ($values as $v) {
				$result[] = 'SADD "' . addslashes($key) . '" "' . addslashes($v) . '"';
			}
			return implode(PHP_EOL, $result);
		}
	
		// ZSet
		else if ($type == 'zset') {
			$values = $redis -> zRange($key, 0, -1);
			$result = array();	
	
			foreach ($values as $v) {
				$s = $redis -> zScore($key, $v);					
				$result[] = 'ZADD "' . addslashes($key) . '" ' . $s . ' "' . addslashes($v) . '"';
			}
			return implode(PHP_EOL, $result);
		}
	}
	
	
	
	/**
	 *
	 * 导出数据为json格式
	 * @param $key
	 */
	private function _export_json($key)
	{
		$redis = $this -> redis_model -> get_redis_instance();
		$redis_types = $this -> redis_model -> get_redis_types();
	
		$type = $redis -> type($key);
	
		if ( ! isset($redis_types[$type]) ) {
			return 'undefined';
		}
	
		$type = $redis_types[$type];
	
	
		// String
		if ($type == 'string') {
			$value = $redis -> get($key);
		}
	
		// Hash
		else if ($type == 'hash') {
			$value = $redis -> hGetAll($key);
		}
	
		// List
		else if ($type == 'list') {
			$size  = $redis -> lSize($key);
			$value = array();
	
			for ($i = 0; $i < $size; ++$i) {
				$value[] = $redis -> lGet($key, $i);
			}
		}
	
		// Set
		else if ($type == 'set') {
			$value = $redis -> sMembers($key);
		}
	
		// ZSet
		else if ($type == 'zset') {
			$value = $redis -> zRange($key, 0, -1);
		}
	
	
		return $value;
	}
}
