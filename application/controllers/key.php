<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Key extends MY_Controller {
	
	
	protected $_auto_build_tree_level = 1;
	
	protected $_redis_types;
	
	protected $_sub_key;
	
	public function __construct()
	{
		parent::__construct();
		$this -> _current_method = '';

		$this -> _redis_types = $this -> redis_model -> get_redis_types();
	}

	public function index()
	{
		$sub_key = get_arg('key', '', 'trim');
		$this -> _sub_key = $sub_key;
		
		$ks = ($sub_key !== '') ? explode(SEPERATOR, $sub_key) : array();
		$this -> _auto_build_tree_level = count($ks) + 1;
		
		$redis_keys = $this -> redis_model -> get_all_keys($sub_key);

		$namespaces = array();
		$this -> keys_to_tree($redis_keys, $namespaces);
		
		$key_name = '';
		if ( ( ! empty($ks) ) 
			&& ( ! empty($namespaces) )
		){
			foreach($ks as $k) {
				$key_name = $k;
				$namespaces = $namespaces[$key_name];
			}
		}
		
		$is_end = 1;
		$step = 1;
		$key_tree = array();
		if ( ( ! empty($namespaces) )
			&& ( is_array($namespaces) )
		){
			$step = get_arg('step', 1, 'intval');
			$step = $step >= 1 ? $step : 1;
			

			$offset = ($step - 1) * TREE_KEY_PAGE_SIZE;
			$cnt = count($namespaces);

			if ( $offset + TREE_KEY_PAGE_SIZE >= $cnt ) {
				$is_end = 1;
			} else {
				$step += 1;
				$is_end = 0;
			}
			
			$namespaces = array_slice($namespaces, $offset, TREE_KEY_PAGE_SIZE, TRUE);
			
			$this -> _create_key_tree($namespaces, $key_name, $sub_key, empty($namespaces), $key_tree);
			
			
		}
		
		$key_tree = implode(PHP_EOL, $key_tree);
		$ret_arr = array(
				'html' => $key_tree,
				'isEnd' => $is_end,
				'step' => $step,
		);
			
		show_message(0, '', $ret_arr);
	}
	
	/**
	 *
	 * 建树
	 * @param unknown_type $item
	 * @param unknown_type $name
	 * @param unknown_type $full_key
	 * @param unknown_type $is_last
	 * @param unknown_type $return_html
	 */
	private function _create_key_tree($item, $name, $full_key, $is_last, & $return_html)
	{
	
		$this -> _nowLevel = ( $full_key !== '' ) ? count(explode(SEPERATOR, $full_key)) : 0;
		
		$this -> _create_key_tree_head($item, $name, $full_key, $is_last, $return_html);
		
		if (count($item) > 0) {
			$tree_sub2 = array();
			if ( $full_key != $this -> _sub_key ) {
				//因为dom的ID不能包含冒号: 所以将:替换为___
				$tree_sub2[] = '<li class="folder' . (empty($full_key) ? '' : ' collapsed') . ($is_last ? ' last' : '') .'" title="' . urlencode($full_key) . '" id="keyid_' . str_replace(SEPERATOR, '___', $full_key) . '">';
				$tree_sub2[] = '<div class="icon">' . format_html($name) . '&nbsp;<span class="info">(' . count($item) . ')</span>';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="#" class="refresh" onclick="return refreshTree(this)"><img src="' . base_url('static/images/refresh.png') . '" width="10" height="10" title="刷新当前节点" alt="[X]"></a>' : '';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="' . manager_site_url('export', 'index', 'key=' . urlencode($full_key) . ':*') . '" class="export"><img src="' . base_url('static/images/export.png') . '" width="10" height="10" title="导出当前节点" alt="[X]"></a>' : '';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="' . manager_site_url('delete', 'index', 'tree=' . urlencode($full_key) . ':') . '" class="deltree"><img src="' . base_url('static/images/delete.png') . '" width="10" height="10" title="删除整个树" alt="[X]"></a>' : '';
				$tree_sub2[] = '</div>';
			}
			$tree_sub2[] = '<ul>';
	
			$return_html[] = implode(PHP_EOL, $tree_sub2);
	
			if ( ( $this -> _nowLevel < $this -> _auto_build_tree_level )
				&& ( is_array($item) )
			){
				$l = count($item);
	
				foreach ($item as $childname => $childitem) {
	
					if (empty($full_key)) {
						$childfullkey = $childname;
					} else {
						$childfullkey = $full_key. SEPERATOR . $childname;
					}
	
					$this -> _create_key_tree($childitem, $childname, $childfullkey, (--$l == 0), $return_html);
				}
			}
			$return_html[] = '</ul>';
				
			if ( $full_key != $this -> _sub_key ) {
				$return_html[] = '</li>';
			}
		}
	}
	
}
