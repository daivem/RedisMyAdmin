<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 导入数据
 * @author DV
 *
 */
class Import extends MY_Controller {
	

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
			
		$page_data = $this -> get_default_page_data();


		$page_data['title'] = '导入数据';
		$this -> load -> view('import', $page_data);
		
	}
	

	private function _do_index()
	{
		$commands = $this -> input -> post('commands');
	
		$commands = str_getcsv(str_replace(array("\r", "\n"), array('', ' '), $_POST['commands']).'    ', ' ');
	
		foreach ($commands as &$command) {
			$command = stripslashes($command);
		}
		unset($command);
	
		for ($i = 0; $i < count($commands); ++$i) {
				
			if (empty($commands[$i])) {
				continue;
			}
	
			$redis = $this -> redis_model -> get_redis_instance();				
			$commands[$i] = strtoupper($commands[$i]);
	
			switch ($commands[$i]) {
				case 'SET': {
					$redis -> set($commands[$i+1], $commands[$i+2]);
					$i += 2;
					break;
				}
					
				case 'HSET': {
					$redis -> hSet($commands[$i+1], $commands[$i+2], $commands[$i+3]);
					$i += 3;
					break;
				}
					
				case 'LPUSH': {
					$redis -> lPush($commands[$i+1], $commands[$i+2]);
					$i += 2;
					break;
				}
					
				case 'RPUSH': {
					$redis -> rPush($commands[$i+1], $commands[$i+2]);
					$i += 2;
					break;
				}
					
				case 'LSET': {
					$redis -> lSet($commands[$i+1], $commands[$i+2], $commands[$i+3]);
					$i += 3;
					break;
				}
					
				case 'SADD': {
					$redis -> sAdd($commands[$i+1], $commands[$i+2]);
					$i += 2;
					break;
				}
					
				case 'ZADD': {
					$redis -> zAdd($commands[$i+1], $commands[$i+2], $commands[$i+3]);
					$i += 3;
					break;
				}
			}
		}
		
		die('操作已完成！');
	
	}
}
