<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 统计信息
 * @author DV
 *
 */
class Info extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		
		$redis = $this -> redis_model -> get_redis_instance();
		
		$reset = get_arg('reset', 0, 'intval');
		$can_reset = method_exists($redis, 'resetStat');
		if ( ($reset)
			&& ( $can_reset)
		){
			$redis -> resetStat();
			$url = manager_site_url('info', 'index');
			header('Location: ' . $url);
			exit;
		}
		
		
		$info = $redis -> info();
		$page_data = $this -> get_default_page_data();
		$page_data['info'] = $info;
		$page_data['can_reset'] = $can_reset;
		$page_data['title'] = '统计信息';
		
		$this -> load -> view('info', $page_data);
	}
	
	
}
