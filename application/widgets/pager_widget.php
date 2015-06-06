<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PagerWidget {
	
 	public static function header()
    {
    	$widget_template = 'header';
    	self::get_widget($widget_template);
    }
    
    public static function footer()
    {
    	$widget_template = 'footer';
    	self::get_widget($widget_template);
    }
    
	public static function get_widget($widget_template, $page_data = array())
	{
    	$CI = & get_instance();    	
    	$CI -> load -> view('widget/' . $widget_template, $page_data);
	}
    
    
}
