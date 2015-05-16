<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 保存数据
 * @author DV
 *
 */
class Save extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$redis = $this -> redis_model -> get_redis_instance();
		
		echo '保存中...<br />';
		
		flush();
		$redis -> save();
		
		echo '完成！';
	}
}
