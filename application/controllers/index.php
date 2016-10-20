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
		$db_size = $this -> redis_model -> get_db_size();  
		$db_size_critical = get_custom_config('config_global', 'db_size_critical');
		$over_critical = ( ( $db_size_critical > 0 ) && ( $db_size >= $db_size_critical ) );

		$page_data = $this -> get_default_page_data();
		$page_data['server_list'] = $this -> server_list;
		$page_data['db_size'] = $db_size;
		$page_data['db_size_critical'] = $db_size_critical;
		$page_data['over_critical'] = $over_critical;
		
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
	
	public function clear_idle_key()
	{
		$this -> _current_method = 'clear_idle_key';
		$this -> index();
	}
	
	
}
