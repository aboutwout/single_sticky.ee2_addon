<?php

/**
* @package ExpressionEngine
* @author Wouter Vervloet
* @copyright  Copyright (c) 2010, Baseworks
* @license    http://creativecommons.org/licenses/by-sa/3.0/
* 
* This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
* To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
* or send a letter to Creative Commons, 171 Second Street, Suite 300,
* San Francisco, California, 94105, USA.
* 
*/

if ( ! defined('EXT')) { exit('Invalid file request'); }

if (! defined('SS_NAME'))
{
  // get the version from config.php
  require PATH_THIRD.'single_sticky/config.php';
  
  define('SS_NAME', $config['name']);
  define('SS_VERSION', $config['version']);
  define('SS_DESCRIPTION', $config['description']);
  
}

class Single_sticky_ext
{
  
  public $settings            = array();
  
  public $name                = SS_NAME;
  public $version             = SS_VERSION;
  public $description         = SS_DESCRIPTION;
  public $settings_exist      = 'y';
  public $docs_url            = '';

	// -------------------------------
	// Constructor
	// -------------------------------
	function Single_sticky_ext($settings='')
	{
	  $this->__construct($settings);    
	}
	
	function __construct($settings='')
	{	  
		$this->settings = $settings;
		$this->EE =& get_instance();
	}
	// END Single_sticky_ext
	
  /**
  * Check entries
  */
  function check_entries($channel_id=0, $autosave=false)
  {
    
    $channel_id = $this->EE->input->post('channel_id', true);
    $is_sticky = ( $this->EE->input->post('sticky', true) == 'y' );
    $entry_id = $this->EE->input->post('entry_id', true);
    
    if( !$channel_id || $autosave === true || !$is_sticky ) return;
    
    if( isset($this->settings[$channel_id]) && $this->settings[$channel_id] == 'n' ) return;
        
    $data = array('sticky' => 'n');
    $where = array('channel_id' => $channel_id, 'sticky' => 'y', 'entry_id <>' => $entry_id);
    $this->EE->db->update('channel_titles', $data, $where);
    
  }
  // END check_entries
  
  /**
  * Modifies control panel html by adding the Auto Expire
  * settings panel to Admin > channel Administration > channel Management > Edit channel
  */
  function settings_form($settings=array())
  {
    
    $this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('single_sticky_extension_name'));  

    $channel_query = $this->EE->db->select("channel_id, channel_title")->get("channels");
    $channels = array();
      
    foreach($channel_query->result() as $row)
    {
      $channels[] = array(
        'id' => $row->channel_id,
        'title' => $row->channel_title,
        'enabled' => ( isset($settings[$row->channel_id]) ) ? $settings[$row->channel_id] : 'n'
      );
    }
    
    $vars = array(
      'channels' => $channels,
      'settings_saved' => $_SERVER['REQUEST_METHOD'] == 'POST'
    );
    
    return $this->EE->load->view('settings_form', $vars, TRUE);
    
  }
  // END settings_form

  /**
  * Save settings
  */
  function save_settings()
  {
    
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->EE->input->post('ss_enabled', true) )
    {
      $this->settings = $this->EE->input->post('ss_enabled', true);
      
      $data = array('settings' => serialize($this->settings));
      $where = 'class = "'.get_class($this).'"';
      $this->EE->db->update('extensions', $data, $where);    
    }
    
  }
  // END save_settings

  /**
  * Activate Extension
  */
	function activate_extension()
	{
	  
    // hooks array
    $hooks = array(
      'entry_submission_start' => 'check_entries'
    );

    // insert hooks and methods
    foreach ($hooks AS $hook => $method)
    {
      // data to insert
      $data = array(
        'class'		=> get_class($this),
        'method'	=> $method,
        'hook'		=> $hook,
        'priority'	=> 1,
        'version'	=> $this->version,
        'enabled'	=> 'y',
        'settings'	=> ''
      );

      // insert in database
      $this->EE->db->insert('extensions', $data);
    }

    return true;
	}
	// END activate_extension
	 
	 
	// --------------------------------
	//  Update Extension
	// --------------------------------  
	function update_extension($current='')
	{
  }
  // END update_extension

	// --------------------------------
	//  Disable Extension
	// --------------------------------
	function disable_extension()
	{	
    // Delete records
    $this->EE->db->where('class', "'".get_class($this)."'")->delete('extensions');
  }
  // END disable_extension

	 
}
// END CLASS