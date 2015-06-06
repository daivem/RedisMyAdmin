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
		 * 建树时使用的标志
		 */
		define('TREE_END_SIGN', get_custom_config('config_global', 'tree_end_sign'));

		/*
		 * KEY的最大长度
		 */
		define('MAX_KEY_LEN', get_custom_config('config_global', 'max_key_len'));
		
		/*
		 * 快速模式
		 * （开启后不显示单个KEY的Item总数）
		 */
		define('FAST_MODEL', get_custom_config('config_global', 'faster_model'));
		
		/*
		 * 异步加载建树时，一次读取的key的数量
		 */
		define('TREE_KEY_PAGE_SIZE', get_custom_config('config_global', 'tree_key_page_size'));
		
		define('PROJECT_NAME', get_custom_config('config_global', 'project_name'));
		define('VERSION', get_custom_config('config_global', 'version'));

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
	
	
	public function keys_to_tree($redis_keys, & $namespaces)
	{

		if ( ! empty($redis_keys) ) {
			foreach ($redis_keys as $key) {					
				//限制KEY长度，Redis支持很长的KEY，但在URL中太长浏览器不一定支持
				if ( isset($key[MAX_KEY_LEN + 1]) ) {
					continue;
				}
					
				$key = explode(SEPERATOR, $key);
				$len = count($key);
		
				$d = & $namespaces;
				for ($i = 0; $i < ($len - 1); ++$i) {
					if ( ! isset($d[$key[$i]]) ) {
						$d[$key[$i]] = array();
					}
					$d = & $d[$key[$i]];
				}
					
				$d[$key[count($key) - 1]] = array(TREE_END_SIGN => TRUE);
					
				unset($d);
			}
		
		}
	}
	
	protected function _create_key_tree_head( & $item, $name, $full_key, $is_last, & $return_html)
	{
		if ( isset($item[TREE_END_SIGN]) ) {
			//需要unset掉，否则会影响到节点的判断
			unset($item[TREE_END_SIGN]);
		
			
			$class = array();
			$len   = FALSE;
			
			$current = get_arg('key');
		
			if ( $current && ($full_key == $current)) {
				$class[] = 'current';
			}
			if ($is_last) {
				$class[] = 'last';
			}
		
			$redis = $this -> redis_model -> get_redis_instance();

			if ( ! FAST_MODEL ) {
				$type = $this -> redis_model ->  get_key_type($full_key);
					
				if ( !isset($this -> _redis_types[$type]) ) {
					return;
				}
				
				$type  = $this -> _redis_types[$type];				
				switch ($type) {
					case 'hash':
						$len = $redis -> hLen($full_key);
						break;
							
					case 'list':
						$len = $redis -> lSize($full_key);
						break;
							
					case 'set':
						$len = count($redis -> sMembers($full_key));
						break;
							
					case 'zset':
						$len = count($redis -> zRange($full_key, 0, -1));
						break;
				}
			}
		
			$tree_sub1 = array();
			$tree_sub1[] =  '<li' . (empty($class) ? '' : ' class="' . implode(' ', $class) . '"') . '>';
			$tree_sub1[] = '<a href="' . manager_site_url('view', 'index', 'key=' . urlencode($full_key)) .'" target="iframe">' . format_html($name);
			$tree_sub1[] = ($len !== FALSE) ? '<span class="info">(' . $len . ')</span>' : '';
			$tree_sub1[] = '</a></li>';
				
			$return_html[] = implode(PHP_EOL, $tree_sub1);		
		}
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