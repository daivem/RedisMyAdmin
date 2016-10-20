<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 清理空闲key
 * @author DV
 *
 */
class Clear_idle_key extends MY_Controller {

	private $_idle_key;

	public function __construct()
	{
		parent::__construct();
		$this -> _idle_key = get_custom_config('config_global', 'idle_key');
	}

	public function index()
	{
		$db_size = $this -> redis_model -> get_db_size();
				
		$page_data = $this -> get_default_page_data();
		$page_data['db_size'] = $db_size;
		$page_data['title'] = '检索/删除 空闲key';
		
		$this -> load -> view('clear_idle_key', $page_data);
	}

	public function view_idle_key_list()
	{
		$iterator = get_arg('iterator', -1, 'intval');
		$idle_time = get_arg('idle_time', 0, 'intval');
		$key_prefix = get_arg('key_prefix', '', 'trim');
		if ( $idle_time < 5 ) {
			show_message(1, sprintf('设置的时间[%d]过短，空闲时间不建议小于5秒', $idle_time));

		}
		$iterator = ($iterator === -1) ? NULL : $iterator;
		$sub_key = '';
		if ( $key_prefix != '' ) {
			$sub_key = $key_prefix . '*';
		}
		$result = $this -> redis_model -> scan_keys($sub_key, $iterator, TRUE, IDLE_KEY_PAGE_SIZE);
		$redis_keys = $result['keys'];
		$new_iterator = $result['iterator'];
		$ret_arr = array();
		if ( ! empty($redis_keys) ) {
			$redis = $this -> redis_model -> get_redis_instance();
			foreach($redis_keys as $key) {
				$t = $redis -> object('idletime', $key);
				if ( $t >= $idle_time ) {
					$ret_arr[] = array(
							'k' => htmlspecialchars($key),
							't' => $t,
						);
				}
			}
		}
		show_message(0, '', array(
				'iterator' => $new_iterator,
				'idle_keys' => $ret_arr,
			));
	}
	
	public function clear_keys()
	{
		$iterator = get_arg('iterator', -1, 'intval');
		$idle_time = get_arg('idle_time', 0, 'intval');
		$key_prefix = get_arg('key_prefix', '', 'trim');
		if ( $idle_time < 5 ) {
			show_message(1, sprintf('设置的时间[%d]过短，空闲时间不建议小于5秒', $idle_time));

		}
		$iterator = ($iterator === -1) ? NULL : $iterator;
		$sub_key = '';
		if ( $key_prefix != '' ) {
			$sub_key = $key_prefix . '*';
		}
		$result = $this -> redis_model -> scan_keys($sub_key, $iterator, TRUE, IDLE_KEY_PAGE_SIZE);
		$redis_keys = $result['keys'];
		$new_iterator = $result['iterator'];
		$ret_arr = array();
		$affected = 0;
		if ( ! empty($redis_keys) ) {
			$redis = $this -> redis_model -> get_redis_instance();
			foreach($redis_keys as $key) {
				$t = $redis -> object('idletime', $key);
				if ( $t >= $idle_time ) {
					$redis -> delete($key);
					$affected += 1;
				}
			}
		}
		show_message(0, '', array(
				'iterator' => $new_iterator,
				'affected' => $affected,
			));
	}
}
