<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 重命名
 * @author DV
 *
 */
class Ttl extends MY_Controller {
	

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

		$key = $this -> input -> get('key');
		$key = get_arg('key');
		if ($key === NULL) {
			show_error('没有找到参数Key');
		}
		
		$redis = $this -> redis_model -> get_redis_instance();
		$key_exists = $redis -> exists($key);
		if ( ! $key_exists ) {
			show_error('Key[' . $key . ']不存在');
		}
		
		$ttl = get_arg('ttl');
		($ttl === NULL) && ( $ttl = '');
		
		$page_data = $this -> get_default_page_data();
		$page_data['key'] = $key;
		$page_data['ttl'] = $ttl;
		$page_data['title'] = '修改生存期';
		
		$this -> load -> view('ttl', $page_data);
		
	}
	
	private function _do_index()
	{
		$key = get_post_arg('key');
		
		$redis = $this -> redis_model -> get_redis_instance();
		
		$key_exists = $redis -> exists($key);
		
		if ( ! $key_exists ) {
			show_error('Key[' . $key . ']不存在');			
		}
		
		$ttl = get_post_arg('ttl');
		if ( $ttl == '-1' ) {
			$redis -> persist($key);
		} else {
			$redis -> expire($key, $ttl);
		}
			
		$url = manager_site_url('view', 'index', 'key=' . urlencode($key));
		Header('Location:' . $url);
		exit;
	}	
}
