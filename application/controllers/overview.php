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
		
		
		$info = array();
		$page_data = $this -> get_default_page_data();
		if ( $view_all ) {
			//读取全部服务器
			foreach($this -> server_list as $i => $server) {
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
		
		} else {
			//只看当前服务器
			$redis = new Redis();
			$server = $this -> redis_config;
			$i = SERVER_ID;
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
				
			$page_data['info'] = $info;
			$server_list = array();
			$server_list[SERVER_ID] = $server;
			$page_data['server_list'] = $server_list;
		}
		
		$page_data['title'] = '服务器一览表';
		
		$this -> load -> view('overview', $page_data);
	}
	
	
}
