<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 服务器一览表
 * @author DV
 *
 */
class Overview extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ( ( empty($this -> server_list) )
			|| ( ! is_array($this -> server_list) )
		){
			show_error('Server List Invaild');
		}
		
		$view_all = get_arg('viewall', 0, 'intval');
		
		if ( $view_all ) {
			//查看全部服务器
			$this -> _overview_all();
			return ;
		} elseif ( ! CLUSTER_MODE ) {
			//只看当前服务器
			//普通redis服务器
			$this -> _overview_single();
			return ;
		} else {
			//只看当前服务器
			//集群服务器
			$this -> _overview_cluster();
			return ;
		}
	}
	
	/**
	 * 查看全部服务器
	 */
	private function _overview_all()
	{		
		$info = array();
		foreach($this -> server_list as $i => $server) {
			if ( isset($server['cluster_list']) ) {
				$info[$i]['error'] = FALSE;
				$info[$i]['cluster_list'] = $server['cluster_list'];
				continue;
			}
			$redis = new Redis();
	
			$can_connect = FALSE;
			try {
				$can_connect = $redis -> connect($server['host'], $server['port'], 0.5);
			} catch (Exception $e) {
				$can_connect = TRUE;
			}
	
			$info[$i] = array('error' => FALSE);
	
			if ( ! $can_connect ) {
				$info[$i]['error'] = TRUE;
			} else {
				$info[$i] = array_merge($info[$i], $redis -> info());
				$info[$i]['size'] = $redis -> dbSize();
			}
		}
	
	
	
		$page_data['info'] = $info;
		$page_data['server_list'] = $this -> server_list;
			
		$page_data['title'] = '服务器一览表';
			
		$this -> load -> view('overview', $page_data);
	}
	
	/**
	 * 只看当前服务器
	 * 普通redis服务器
	 */
	private function _overview_single()
	{
		$info = array();
		$page_data = $this -> get_default_page_data();
		$redis = $this -> redis_model -> get_redis_instance();
		$server = $this -> redis_config;
		$i = SERVER_ID;
			
		$info[$i] = array('error' => FALSE);
			
		$info[$i] = array_merge($info[$i], $redis -> info());
		$info[$i]['size'] = $redis -> dbSize();
			
		$page_data['info'] = $info;
		$server_list = array();
		$server_list[SERVER_ID] = $server;
		$page_data['server_list'] = $server_list;
		
		$page_data['title'] = '服务器概况';
		
		$this -> load -> view('overview', $page_data);
	}
	
	/**
	 * 只看当前服务器
	 * 集群服务器
	 */
	private function _overview_cluster()
	{
		$page_data = $this -> get_default_page_data();
		$redis = $this -> redis_model -> get_redis_instance();
		$page_data['infos'] = array();
		foreach($redis -> _masters() as $cluster_config) {
			$info = $redis -> info($cluster_config);
			$name = implode(':', $cluster_config);
			if ( isset($info['db0']) ) {
				$arr = explode(',', $info['db0']);
				$key_infos = array();
				foreach($arr as $v) {
					list($key, $value) = explode('=', $v);
					$key_infos[$key] = $value;
				}
				$info['key_infos'] = $key_infos;
			}
			$page_data['infos'][$name] = $info;
		}
		
		$page_data['title'] = '集群概况';
		$page_data['server_name'] = $this -> redis_config['name'];
		$this -> load -> view('overview_cluster', $page_data);
	}
}
