<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Key extends MY_Controller {
	
	
	protected $_auto_build_tree_level = 1;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$sub_key = get_arg('key', '', 'trim');
		$iterator = get_arg('iterator', -1, 'intval');
		$iterator = ($iterator === -1) ? NULL : $iterator;
		
		$ks = ($sub_key !== '') ? explode(SEPERATOR, $sub_key) : array();
		$this -> _auto_build_tree_level = count($ks) + 1;
		
		$result = $this -> redis_model -> scan_keys($sub_key, $iterator, TRUE);
		$redis_keys = $result['keys'];
		$new_iterator = $result['iterator'];
		$ret_arr = array(
				'keys' => $redis_keys,
				'iterator' => $new_iterator,
		);
			
		show_message(0, '', $ret_arr);
	}
	
	
}
