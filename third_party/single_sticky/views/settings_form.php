<?php if( count($channels) == 0) : ?>
<p style="margin-bottom:1.5em">You haven't created any channels yet. Go to the <a href="<?=BASE.AMP.'M=blog_admin'.AMP.'P=new_weblog';?>">Channel Management</a> and create one first.</p>
<?php 
  else : 

  echo form_open(  'D=cp'.AMP.'C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file=single_sticky', array(), array('name' => 'single_sticky'));

  $this->table->set_template(array( 'table_open'  => '<table class="mainTable" border="0" cellpadding="0" cellspacing="0">' ));

  $this->table->set_heading(
  	lang('channel'),
  	lang('single_sticky_enable')
  );
  
  foreach($channels as $channel)
  {
    $this->table->add_row(
    	$channel['title'],
    	'<label>'.form_radio('ss_enabled['.$channel['id'].']', 'y', ($channel['enabled'] == 'y')).' '.lang('yes').'</label>'.NBS.NBS.
      '<label>'.form_radio('ss_enabled['.$channel['id'].']', 'n', ($channel['enabled'] == 'n')).' '.lang('no').'</label>'
  	);      
  }
  
  echo $this->table->generate();
  
?>
<div style="padding:10px 0"><input type="submit" value="<?=lang('save')?>" class="submit" /></div>
<?=  form_close(); ?>
<?php endif; ?>