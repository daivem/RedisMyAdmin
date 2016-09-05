<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 查看空闲key
 * @author DV
 *
 */
class Idle extends MY_Controller {

	private $_idle_key;

	public function __construct()
	{
		parent::__construct();
		$this -> _idle_key = get_custom_config('config_global', 'idle_key');
	}

	public function index()
	{
		$idle_infos = $this -> _get_idle_report();
		$db_size = $this -> redis_model -> get_db_size();
				
		$page_data = $this -> get_default_page_data();
		$page_data['idle_infos'] = $idle_infos;
		$page_data['db_size'] = $db_size;
		$page_data['title'] = '查看空闲key列表';
		
		$this -> load -> view('idle', $page_data);
	}
	
	public function build_report()
	{
		if ( ! $this -> is_post() ) {
			header('Location:' . manager_site_url(strtolower(__CLASS__), 'index'));
			exit;
		}
		$cnt = get_arg('cnt', 10, 'intval');
		$cnt = $cnt <= 0 ? 10 : $cnt;
		
		$redis = $this -> redis_model -> get_redis_instance();
		$keys = $this -> redis_model -> get_all_keys_by_scan('', FALSE);
		// $keys = $redis -> keys('*');
		
		$head = IdleQueue::factory(-1, $cnt);
		
		foreach($keys as $key) {
			if ( $key == $this -> _idle_key ) {
				continue;
			}
			$d = $redis -> object('idletime', $key);
			
			$tmp = & $head;
			while( $tmp !== NULL && $d >= $tmp -> idle_time ) {
				$tmp = & $tmp -> next;
			}
		
			if ( $tmp === $head ) {
				continue;
			}
		
			$head -> idle_time = $d;
			$head -> key = $key;
			if ( $tmp != $head -> next ) {
				$t = $head;
				$head = $head -> next;
				$t -> next = is_null($tmp) ? NULL : $tmp;
				$tmp = $t;
			}
		}
		
		$report = IdleQueue::result($head);
		$this -> _set_idle_report($report);
		die('操作已完成！ <a href="' . manager_site_url('idle', 'index') . '">返回</a>');
		
	}
	
	private function _get_idle_report()
	{
		$error_ret = array();
		$redis = $this -> redis_model -> get_redis_instance();
		if ( ! $redis -> exists($this -> _idle_key) ) {
			return $error_ret;
		}
		
		$fields = array('timestamp', 'idle_list');
		$ret_arr = $redis -> hmget($this -> _idle_key, $fields);
		foreach($ret_arr as $k => $v) {
			if ( $v === FALSE ) {
				return $error_ret;
			}
		}
		
		$ret_arr['idle_list'] = json_decode($ret_arr['idle_list'], TRUE);
		return $ret_arr;
	}
	
	private function _set_idle_report($report)
	{
		$redis = $this -> redis_model -> get_redis_instance();
		$redis -> hset($this -> _idle_key, '_readme', '这是空闲key列表的缓存,如需清除请将整个key删除');
		$redis -> hset($this -> _idle_key, 'timestamp', time());
		$redis -> hset($this -> _idle_key, 'idle_list', json_encode($report));
	}
	
}

class IdleQueue {
	public $idle_time;
	public $key;
	public $next = NULL;

	public function __construct($idle_time) {
		$this -> idle_time = $idle_time;
	}

	public static function factory($idle_time, $max_len) {
		$head = NULL;
		$prev = NULL;
		for ($i = 0; $i < $max_len; $i++) {
			$node = new IdleQueue($idle_time);
			if ( is_null($head) ) {
				$head = $node;
			}

			if ( ! is_null($prev) ) {
				$prev -> next = $node;
			}

			$prev = $node;
		}
		return $head;
	}

	public static function result($node) {
		$ret_arr = array();
		while ( ! is_null($node) ) {
			if ($node -> idle_time != -1) {
				array_unshift(
					$ret_arr, 
					array(
						'key' => $node -> key,
						'idle_time' => $node -> idle_time,	
					)
				);
			}
			
			$node = $node -> next;
		}
		return $ret_arr;
	}
}

