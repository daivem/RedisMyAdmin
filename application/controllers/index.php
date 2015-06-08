<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {
	
	protected $_current_method;
	
	protected $_auto_build_tree_level = 1;
	
	protected $_redis_types;
	
	public function __construct()
	{
		parent::__construct();
		$this -> _current_method = '';

		$this -> _redis_types = $this -> redis_model -> get_redis_types();
	}

	public function index()
	{
		if ( '' === KEY_PREFIX ) {
			$db_size = $this -> redis_model -> get_db_size();  
		} else {
			$redis_keys = $this -> redis_model -> get_all_keys(KEY_PREFIX);
			$db_size = count($redis_keys);
			unset($redis_keys);
		}
		
		$db_size_critical = get_custom_config('config_global', 'db_size_critical');
		$hide_tree = ( ( $db_size_critical > 0 ) && ( $db_size >= $db_size_critical ) );
					
		$page_data = $this -> get_default_page_data();
		$page_data['server_list'] = $this -> server_list;
		$page_data['db_size'] = $db_size;
		$page_data['db_size_critical'] = $db_size_critical;
		
		$page_data['html_key_tree'] = FALSE;
		if ( ! $hide_tree ) {
			$page_data['html_key_tree'] = $this -> get_key_tree(TRUE);
		}
		
		if ( ! $this -> _current_method ) {
			$page_data['iframe_url'] = manager_site_url('overview', 'index');
		} else {
			$query = $this -> input -> get();
			$query['c'] = $this -> _current_method;
			$query['m'] = 'index';
				
			$page_data['iframe_url'] = site_url(http_build_query($query));
		}
		
		$page_data['title'] = PROJECT_NAME;
		
		$this -> load -> view('index', $page_data);
	}
	
	public function get_key_tree($return_only = FALSE)
	{
		$redis_keys = $this -> redis_model -> get_all_keys(KEY_PREFIX);
		$namespaces = array();
		$this -> keys_to_tree($redis_keys, $namespaces);
		
		$key_tree = array();
		$key_tree[] = '<ul>';
		$this -> _create_key_tree($namespaces, 'Keys', '', empty($namespaces), $key_tree);
		$key_tree[] = '</ul>';
		$key_tree = implode(PHP_EOL, $key_tree);
		if ( $return_only ) {
			return $key_tree;
		} else {
			show_message(0, '', array('html' => $key_tree));
		}
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
		$this -> _now_level = count(explode(SEPERATOR, $full_key));
	
		$this -> _create_key_tree_head($item, $name, $full_key, $is_last, $return_html);
		
		if (count($item) > 0) {
			if ( ( $this -> _now_level <= $this -> _auto_build_tree_level )
				&& ( is_array($item) )
			){
				$tree_sub2 = array();
				//因为dom的ID不能包含冒号: 所以将:替换为___
				$tree_sub2[] = '<li class="folder' . (empty($full_key) ? '' : ' collapsed') . ($is_last ? ' last' : '') .'" title="' . urlencode($full_key) . '" id="keyid_' . str_replace(SEPERATOR, '___', $full_key) . '">';
				$tree_sub2[] = '<div class="icon">' . format_html($name) . '&nbsp;<span class="info">(' . count($item) . ')</span>';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="#" class="refresh" onclick="return refreshTree(this)"><img src="' . base_url('static/images/refresh.png') . '" width="10" height="10" title="刷新整个树" alt="[X]"></a>' : '';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="' . manager_site_url('export', 'index', 'key=' . urlencode($full_key) . ':*') . '" class="export"><img src="' . base_url('static/images/export.png') . '" width="10" height="10" title="导出整个树" alt="[X]"></a>' : '';
				$tree_sub2[] =  (!empty($full_key)) ? '<a href="' . manager_site_url('delete', 'index', 'tree=' . urlencode($full_key) . ':') . '" class="deltree"><img src="' . base_url('static/images/delete.png') . '" width="10" height="10" title="删除整个树" alt="[X]"></a>' : '';
				$tree_sub2[] = '</div>';
				$tree_sub2[] = '<ul>';
				 
	
				$return_html[] = implode(PHP_EOL, $tree_sub2);
	
	
	
				$l = count($item);
	
				foreach ($item as $childname => $childitem) {
	
					if (empty($full_key)) {
						$child_fullkey = $childname;
					} else {
						$child_fullkey = $full_key. SEPERATOR . $childname;
					}
	
					$this -> _create_key_tree($childitem, $childname, $child_fullkey, (--$l == 0), $return_html);
				}
					
				$return_html[] = '</ul></li>';
			}
		}
	}
	

	public function view()
	{
		$this -> _current_method = 'view';
		$this -> index();
	}
	
	
	public function edit()
	{
		$this -> _current_method = 'edit';
		$this -> index();
	}
	
	public function ttl()
	{
		$this -> _current_method = 'ttl';
		$this -> index();
	}
	
	
	public function export()
	{
		$this -> _current_method = 'export';
		$this -> index();
	}
	
	
	public function import()
	{
		$this -> _current_method = 'import';
		$this -> index();
	}
	
	public function rename()
	{
		$this -> _current_method = 'rename';
		$this -> index();
	}
	
	public function overview()
	{
		$this -> _current_method = 'overview';
		$this -> index();
	}
	
	public function info()
	{
		$this -> _current_method = 'info';
		$this -> index();
	}

	public function idle()
	{
		$this -> _current_method = 'idle';
		$this -> index();
	}
	
	
}
