<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 重命名
 * @author DV
 *
 */
class Rename extends MY_Controller {
	

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

		$key = get_arg('key');
		if ($key === NULL) {
			show_error('没有找到参数Key');
		}
		
		$redis = $this -> redis_model -> get_redis_instance();
		$key_exists = $redis -> exists($key);
		if ( ! $key_exists ) {
			show_error('Key[' . $key . ']不存在');
		}
		
		$page_data = $this -> get_default_page_data();
		$page_data['key'] = $key;
		$page_data['title'] = '重命名Key[' . $key . ']';
		
		$this -> load -> view('rename', $page_data);
	}
	
	private function _do_index()
	{
		$key = get_post_arg('key');
		$old_key = get_post_arg('old');

		if ( ($key === NULL) 
			|| ( $key === '' )
		){
			show_error('没有找到新键名参数key');
		}
		
		if ( strlen($key) > MAX_KEY_LEN ) {
			show_error('Key长度[' . strlen($key) . ']超过限制，当前限制为[' . MAX_KEY_LEN . ']');
		}
		
		$redis = $this -> redis_model -> get_redis_instance();		
		$key_exists = $redis -> exists($old_key);		
		if ( ! $key_exists ) {
			show_error('Key[' . $old_key . ']不存在');
		}
		
		$redis -> rename($old_key, $key);
		
		$url = manager_site_url('view', 'index', 'key=' . urlencode($key));
		Header('Location:' . $url);
		exit;
	}	
}
