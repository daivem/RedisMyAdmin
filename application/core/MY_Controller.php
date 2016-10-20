<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


header("Content-type:text/html; charset=UTF-8");

class MY_Controller extends CI_Controller {   
	
	/**
	 * 服务器列表
	 * @var unknown
	 */
	public $server_list;
	
	/**
	 * 当前选择的服务器配置
	 * @var unknown
	 */
	public $redis_config;

	public function __construct(){
		parent::__construct();
		error_reporting(E_ALL);
		ini_set('display_error', TRUE);
		$this -> _check_phpredis_exists();
		
		$this -> load -> helper('url');
		$this -> load -> helper('form');
		$this -> load -> helper('common');
		

		$this -> widget('pager');

		global $RTR;
		
		define('AUTH', get_custom_config('config_auth', 'auth'));
		define('SECCODE_ENABLE', get_custom_config('config_auth', 'seccode_enable'));
		$auth_user = get_custom_config('config_auth', 'auth_user');
		if ( AUTH ) {
			session_start();
		
			if ( ( ! empty($auth_user) )
				&& ( is_array($auth_user) )
				&& ( 'login' !== strtolower( $RTR -> fetch_class() ) )
			){
				$this -> _auth_check();
			}
		}
		
		/*
		 * 服务器列表
		 */
		$this -> server_list = get_custom_config('config_redis', 'server_list');
		
		/*
		 * 当前选择的服务器ID
		 */
		$server_id = get_arg('server_id', 0, 'intval');
		$server_id = isset($this -> server_list[$server_id]) ? $server_id : 0;
		define('SERVER_ID', $server_id);
		
		/*
		 * KEY前缀过滤
		 */
		$prefix = (string)get_arg('prefix', '');
		define('KEY_PREFIX', $prefix);
		
		/*
		 * 当前选择的DB
		 */
		$db = get_arg('db', 0, 'intval');
		$db = $db > 0 ? $db : 0;
		define('CURRENT_DB', $db);
		 
		/*
		 * 建树时用的分隔符，Redis官方推荐使用冒号":"
		 */
		define('SEPERATOR', get_custom_config('config_global', 'seperator'));
		
		/*
		 * KEY的最大长度
		 */
		define('MAX_KEY_LEN', get_custom_config('config_global', 'max_key_len'));
		
		/*
		 * 异步加载建树时，一次读取的key的数量
		 */
		define('TREE_KEY_PAGE_SIZE', get_custom_config('config_global', 'tree_key_page_size'));

		/*
		 * 异步查找空闲Key时，一次读取的key的数量
		 */
		define('IDLE_KEY_PAGE_SIZE', get_custom_config('config_global', 'idle_key_page_size'));
		
		define('PROJECT_NAME', get_custom_config('config_global', 'project_name'));
		define('VERSION', get_custom_config('config_global', 'version'));

		/*
		 * 执行时间
		 */
		$time_limit = get_custom_config('config_global', 'set_time_limit');
		if ( $time_limit >= 0 ) {
			set_time_limit($time_limit);
		}

		/*
		 * 执行时间
		 */
		$memory_limit = get_custom_config('config_global', 'memory_limit');
		if ( ! empty($memory_limit) ) {
			ini_set('memory_limit', $memory_limit);	
		}

		$this -> redis_config = $this -> server_list[SERVER_ID];
		$this -> redis_config['db'] = CURRENT_DB;
		
		if ( isset($this -> redis_config['cluster_list']) ) {
			define('CLUSTER_MODE', TRUE);
		} else{
			define('CLUSTER_MODE', FALSE);
		}
		
		
		$this -> load -> model('redis_model');
		$this -> redis_model -> init($this -> redis_config);
	}
	
	private function _check_phpredis_exists()
	{
		if ( ! class_exists('Redis') ) {
			show_error('缺少核心基类[Redis]，请下载phpredis <a href="https://github.com/phpredis/phpredis" target="_blank">https://github.com/phpredis/phpredis</a>');
		}
	}
	
	private function _check_Redis_cluster_exists()
	{
		if ( ! class_exists('RedisCluster') ) {
			show_error('缺少Redis集群核心类[RedisCluster]，请下载最新支持集群phpredis的版本 <a href="https://github.com/phpredis/phpredis" target="_blank">https://github.com/phpredis/phpredis</a>');
		}
	}

	/**
	 *
	 * widget
	 * @param string $name widget name
	 */
	protected function widget($name = '')
	{
		if (isset($name) && $name != '')
		{
			require_once APPPATH.'widgets/'.$name. '_widget' . EXT;
		}
	}
	
	/**
	 * 
	 * 取得默认的页面数据
	 */
	public function get_default_page_data()
	{
		$page_data = array();
		$page_data['db'] = CURRENT_DB;
		$page_data['prefix'] = KEY_PREFIX;
		$page_data['server_id'] = SERVER_ID;
		$page_data['project_name'] = PROJECT_NAME;
		
		$c = 'index';
		$m = 'index';
		$bt = debug_backtrace();
		if ( count($bt) > 1 ) {
			$c = strtolower($bt[1]['class']);
			$m = strtolower($bt[1]['function']);
		}
		
		$page_data['c'] = $c;
		$page_data['m'] = $m;
		
		return $page_data;
	}
	
	/**
	 * 当前请求是否为POST
	 * @return boolean
	 */
	public function is_post()
	{
		$method = strtolower($this -> input -> server('REQUEST_METHOD'));
		return 'post' === $method;
	}
	
	
	protected function _auth_check()
	{
		if ( ( ! isset($_SESSION['username']) )
			|| ( empty($_SESSION['username']) )
		){
			$cur_url = get_cur_url();
			if ( $cur_url ) {
				$goto = '&goto=' . rawurlencode($cur_url);
			} else {
				$goto = '';
			}
				
			header('location:' . manager_site_url('login', 'index', $goto));
			exit;
		}
	}
	
	protected function _do_login($username, $password)
	{
	
		$auth_user = get_custom_config('config_auth', 'auth_user');
		if ( ( ! isset($auth_user[$username]) )
			|| ( $auth_user[$username] !== $password )
		){
			return FALSE;
		}
	
		$_SESSION['username'] = $username;
		return TRUE;
	
	}
}