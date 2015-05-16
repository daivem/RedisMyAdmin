<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this -> load -> helper('cookie');
	}

	public function index()
	{
		if ( $this -> is_post() ) {
			$this -> _do_index();
		}
		
		if ( ! AUTH ) {
			Header('Location:' . manager_site_url('index', 'index'));
			exit;
		}
		
		$goto = get_arg('goto');
		if ( ! empty($goto) ) {
			set_cookie('goto', $goto, 0);
		}

		$page_data = array();
		$page_data['title'] = '登录 - ' . PROJECT_NAME;
		$page_data['error'] = (int)$this -> input -> get_post('error');
		$page_data['seccode_enable'] = (bool)SECCODE_ENABLE;
		$this -> load -> view('login', $page_data);
	}
	
	/**
	 * 退出登录
	 */
	public function logout()
	{
		session_destroy();
		Header('Location:' . manager_site_url('login', 'index'));
		exit;
	}

	/**
	 * 获取验证码
	 */
	public function seccode()
	{
		if ( ! SECCODE_ENABLE ) {
			die();
		}
		$this -> load -> library('SeccodeImageCaptcha');
		$config = get_custom_config('config_auth', 'seccode_config');
		$text_len = get_custom_config('config_auth', 'seccode_len');
		$lang = get_custom_config('config_auth', 'seccode_lang');
		$seccode_img = new SeccodeImageCaptcha();
		$seccode_img -> init($config, $text_len, $lang);
		$seccode = $seccode_img -> createSeccode();

		$_SESSION['seccode'] = $seccode;
		$seccode_img -> output();
	}
	
	
	private function _do_index()
	{
		$username = $this -> input -> post('username');
		$password = $this -> input -> post('password');
		$username = get_post_arg('username');
		$password = get_post_arg('password');
	
		if ( empty($username)
			|| empty($password)
		){
			Header('Location:' . manager_site_url('login', 'index', 'error=1'));
			exit;
		}
	

		if ( SECCODE_ENABLE ) {
			$seccode = get_post_arg('seccode');
			$sess_seccode = isset($_SESSION['seccode']) ? $_SESSION['seccode'] : NULL;
			
			//读出来后就要删掉
			unset($_SESSION['seccode']);
			
			if ( ( empty($seccode) )
				|| ( empty($sess_seccode) )
				|| ( strtoupper($seccode) !== strtoupper($sess_seccode) )
			){
				Header('Location:' . manager_site_url('login', 'index', 'error=1'));
				exit;
			}	
		}
	
		$success = $this -> _do_login($username, $password);
		if ( $success ) {
			$goto = ( isset($_COOKIE['goto']) )
					? $_COOKIE['goto']
					: manager_site_url('index', 'index');
				
			Header('Location:' . $goto);
			exit;
		} else {
			Header('Location:' . manager_site_url('login', 'index', 'error=1'));
			exit;
		}
	}
	
}
