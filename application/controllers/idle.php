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
		
		if ( ! FAST_MODEL ) {
			show_error('此功能必须在config_global.php中开启faster_model。原因：显示单个Key的Item总数需要读取Key的信息，这将导致Key的空闲时间被刷新。');
		}
		
		$idle_infos = $this -> _get_idle_infos();
		$db_size = $this -> redis_model -> get_db_size();
				
		$page_data = $this -> get_default_page_data();
		$page_data['idle_infos'] = $idle_infos;
		$page_data['db_size'] = $db_size;
		$page_data['title'] = '查看空闲key列表';
		
		$this -> load -> view('idle', $page_data);
	}
	
	private function _get_idle_infos()
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
		
		$ret_arr['idle_list'] = json_decode($ret_arr['idle_list']);
		return $ret_arr;
	}
	
	
	
}
