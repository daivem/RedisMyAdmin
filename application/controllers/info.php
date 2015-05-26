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
		if ( ! CLUSTER_MODE ) {	
			$this -> _info();
			return ;
		} else {
			$this -> _info_cluster();
			return ;
		}
	}
	
	private function _info()
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
	
	private function _info_cluster()
	{
		$redis = $this -> redis_model -> get_redis_instance();

		$page_data = $this -> get_default_page_data();
		$page_data['infos'] = array();
		foreach($redis -> _masters() as $cluster_config) {
			$name = implode(':', $cluster_config);
			$page_data['infos'][$name] = $redis -> info($cluster_config);
		}
		$this -> load -> view('info_cluster', $page_data);
	}
}
