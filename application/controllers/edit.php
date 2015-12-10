<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 编辑数据
 * @author DV
 *
 */
class Edit extends MY_Controller {
	

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
		
		$is_edit = FALSE;
		$key = get_arg('key', NULL);
		$type = get_arg('type', NULL);
		$GETS = array();
		
		$title = '新增Key';
		
		if ( ( $key !== NULL )
			&& ( $type !== NULL )
		){
			$type = strtolower($type);
			$GETS = $this -> input -> get();
			if ( ( $type == 'string' )
				|| ( ( $type == 'hash' ) && ( isset($GETS['hkey']) ) )
				|| ( ( $type == 'list' ) && ( isset($GETS['index']) ) )
				|| ( ( $type == 'set' ) && ( isset($GETS['value']) ) )
				|| ( ( $type == 'zset' ) && ( isset($GETS['value']) ) )
			){
				$is_edit = TRUE;
				$title = '编辑Key[' . $key . ']';
			}
		}

		$redis = $this -> redis_model -> get_redis_instance();
		
		$value = '';
		if ( $is_edit ) {
			if ( $type == 'string' ) {
				//string
				$value = $redis -> get($key);
			} elseif ( ( $type == 'hash' ) && ( isset($GETS['hkey']) ) ) {
				//hash
				$value = $redis -> hGet($key, $GETS['hkey']);
			} elseif ( ( $type == 'list' ) && ( isset($GETS['index']) ) ) {
				//hash
				$value = $redis -> lIndex($key, $GETS['index']);
			} elseif ( ( $type == 'set' || $type == 'zset' ) && isset($GETS['value']) ) {
				//set zset
				$value = $GETS['value'];
			}
		}
		
		$page_data = $this -> get_default_page_data();
		$page_data['is_edit'] = $is_edit;
		$page_data['type'] = $type;
		$page_data['key'] = $key;
		$page_data['value'] = $value;
		$page_data['GETS'] = $GETS;
		$page_data['title'] = $title;
		
		$this -> load -> view('edit', $page_data);
	}
	
	private function _do_index()
	{
		$key = get_post_arg('key', NULL, 'trim');
		if ( ( $key === FALSE )
			|| ( $key === '' )
		){
			show_error('Key不能为空');
		}
		
		if ( strlen($key) > MAX_KEY_LEN ) {
			show_error('Key长度[' . strlen($key) . ']超过限制，当前限制为[' . MAX_KEY_LEN . ']');
		}
		
		$type = strtolower( get_post_arg('type', NULL, 'trim') );
		$allow_type = array('string', 'list', 'hash', 'set', 'zset');
		if ( ! in_array($type, $allow_type) ) {
			show_error('未知的数据类型');
		}
		
		$value = get_post_arg('value');
		$redis = $this -> redis_model -> get_redis_instance();
		
		if ( $type == 'string' ) {
			//string
			$keep_ttl = get_post_arg('keep_ttl', 0, 'intval');
			$orig_ttl = 0;
			if ( $keep_ttl ) {
				$orig_ttl = (int)$redis -> ttl($key);
			}
			$result = $redis -> set($key, $value);
			if ( ! $result ) {
				show_error('操作失败');
			}
			if ($orig_ttl > 0) {
				$redis -> expire($key, $orig_ttl);
			}
		} elseif ( $type == 'hash' ) {
			//hash
			$hkey = get_post_arg('hkey');
			//只有当hkey存在的时候才操作
			if ( $hkey !== NULL ) {
				if ( strlen($hkey) > MAX_KEY_LEN ) {
					show_error('Hash Key长度[' . strlen($key) . ']超过限制，当前限制为[' . MAX_KEY_LEN . ']');
				}
				
				//指定读取$_GET中的hkey
				$old_hkey = get_arg('hkey', NULL, 'trim', 'get');
				if ( ( $old_hkey !== NULL )
					&& ( ! $redis -> hExists($key, $hkey) )
				){
					//如果新的hkey不存在的话
					//删掉原来的旧KEY
					$redis -> hDel($key, $old_hkey);
				}
				
				$redis -> hSet($key, $hkey, $value);
			}
			
		} elseif ( $type == 'list' ) {
			//list
			$size = $redis -> lSize($key);
			
			$index = get_post_arg('index', NULL, 'trim');
			($index === NULL) && ($index = '');
			
			if ( ( $index == '' )
				|| ( $index == $size )
			){
				//加到list最后面
				$redis -> rPush($key, $value);
			} elseif ( $index == '-1' ) {
				//加到list最前面
				$redis -> lPush($key, $value);
			} elseif ( ( $index >= 0 )
						&& ( $index < $size )
			){
				//直接修改原list的值
				$redis -> lSet($key, $index, $value);
			} else {
				show_error('index值越界(Out of bounds index)');
			}

		} elseif ( $type == 'set' ) {
			//set
			$old_value = get_post_arg('oldvalue');
			if ( $value != $old_value ) {
				$redis -> sRem($key, $old_value);
				$redis -> sAdd($key, $value);
			}
			
			
		} elseif ( $type == 'zset' ) {
			//zset
			$old_value = get_post_arg('oldvalue');
			$old_score = get_post_arg('oldscore');
			$score = get_post_arg('score');
			($score === NULL) && ( $score = '');
				
			if ( ( $value != $old_value )
				|| ( $score != $old_score ) 
			){
				$redis -> zDelete($key, $old_value);
				$redis -> zAdd($key, $score, $value);
				
			}
		} 
		
		
		$url = manager_site_url('view', 'index', 'key=' . urlencode($key));
		
		Header('Location:' . $url);
		exit;
	}
	
	

}
