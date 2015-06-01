<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 删除key
 * @author DV
 *
 */
class Delete extends MY_Controller {
	

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ( ! $this -> is_post() ) {
			return ;
		}
		//die('ssss');
		$key = get_arg('key');
		$type = strtolower( get_arg('type') );
		$allow_type = array('' ,'string', 'hash', 'list', 'set', 'zset');
		
		$redis = $this -> redis_model -> get_redis_instance();
		
		if ( ( $key !== NULL ) 
			&& ( $key !== '' )
			&& ( in_array($type, $allow_type) )
		){
			switch($type) {
				default:	//如果传空，即是整key删除
				case 'string':
					$redis -> delete($key);
					
					break;
			
				case 'hash':
					$hkey = $this -> input -> get('hkey');
					$hkey = get_arg('hkey');
					if ( $hkey !== NULL ){
						$redis -> hDel($key, $hkey);
					}
					break;
			
				case 'list':
					$index = get_arg('index');
					if ( $index !== NULL ){
						/*
						 * 说明：
						 * List本身并不具备单独移除单个值的操作
						 * 目前的操作方式为：将此index的值设置为一个很特殊的随机值，然后将此值移出list
						 * 此操作是一个风险点，我们是假定这个随机值是不存在于list中的，而事实上出现相同的机率很低
						 */
						$value = str_rand(69);
						$redis -> lSet($key, $index, $value);
						$redis -> lRem($key, $value, 1);
					}
					break;
			
				case 'set':
					$value = get_arg('value');
					if ( $value !== NULL ){
						$redis -> sRem($key, $value);
					}
					break;
						
				case 'zset':
					$value = get_arg('value');
					if ( $value !== NULL ){
						$redis -> zDelete($key, $value);
					}
					break;
			}
			
			$url = manager_site_url('view', 'index', 'key=' . urlencode($key));
			die($url);
		}

		$tree = $this -> input -> get('tree');
		$tree = get_arg('tree');
		if ( $tree !== NULL ) {
			$tree .= '*';
			$keys = $redis -> keys($tree);
			foreach($keys as $key) {
				$redis -> delete($key);
			}
		}
		
		$url = manager_site_url('index', 'overview');
		die($url);
	}
	
	

}
