<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 查看数据
 * @author DV
 *
 */
class View extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		
		$key = get_arg('key');
		if ($key === NULL) {
			show_error('没有找到参数Key');
		}
		
		$redis = $this -> redis_model -> get_redis_instance();
		
		$page_data = $this -> get_default_page_data();
		
		$key_type = $this -> redis_model -> get_key_type($key);
		$key_exists = $redis -> exists($key);
		
		$page_data['exists'] = $key_exists;
		$page_data['key'] = $key;
		
		$template = 'view_string';
		
		if ( $key_exists ) {		
			$redis_types = $this -> redis_model -> get_redis_types();
			
			$type = $redis_types[$key_type];
			$ttl = $redis -> ttl($key);
			$encoding = $redis -> object('encoding', $key);
			
			switch ($type) {
				case 'string':
					$values = $redis -> get($key);
					$size  = strlen($values);
					$template = 'view_string';
					break;
				
				case 'hash':
					$values = $redis -> hGetAll($key);
					$size   = count($values);
					$template = 'view_hash';
					break;
				
				case 'list':
					$size = $redis -> lSize($key);
					$values = $redis -> lRange($key, 0, -1);
					$template = 'view_list';
					break;
				
				case 'set':
					$values = $redis -> sMembers($key);
					$size   = count($values);
					$template = 'view_set';
					break;
				
				case 'zset':
					$values = $redis -> zRange($key, 0, -1, 1);
					$size   = count($values);
					$template = 'view_zset';
					break;
				}
			
			$page_data['type'] = $type;
			$page_data['values'] = $values;
			$page_data['size'] = $size;
			$page_data['ttl'] = $ttl;
			$page_data['encoding'] = $encoding;
		}

		$page_data['title'] = '查看Key[' . $key . ']';
		$this -> load -> view($template, $page_data);		
	}
}
