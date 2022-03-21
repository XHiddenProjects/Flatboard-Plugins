<?php defined("FLATBOARD") or die("Flatboard community");

function RoleTag_install(){
    	global $lang;
	$plugin = 'RoleTag';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= true; 
    $data['RoleAdmin_label']        = 'Administrator';
    $data['RoleModerator_label']    = 'Moderator';
    $data['RoleGuest_label']        = 'Guest';
    $data['RoleAdmin_btn']          = '#05ff12';
    $data['RoleModerator_btn']      = '#ffff00';
    $data['RoleGuest_btn']          = '#cc00ff';
    $data['RoleAdmin_text']         = '#000000';
    $data['RoleModerator_text']     = '#000000';
    $data['RoleGuest_text']         = '#000000';
    flatDB::saveEntry('plugin', $plugin, $data);
}
function RoleTag_config(){
    global $lang, $token;
	$plugin = 'RoleTag';
    $out = '';
    if(User::isAdmin()){
     if(!empty($_POST) && CSRF::check($token) )
       {
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['RoleAdmin_label'] 		 = HTMLForm::clean($_POST['Admin_label']); 
               $data['RoleModerator_label']      = HTMLForm::clean($_POST['Mod_label']);
               $data['RoleGuest_label'] 		 = HTMLForm::clean($_POST['Guest_label']);
               $data['RoleAdmin_btn']            = HTMLForm::clean($_POST['Admin_palette']);
               $data['RoleModerator_btn']        = HTMLForm::clean($_POST['Mod_palette']);
               $data['RoleGuest_btn']            = HTMLForm::clean($_POST['Guest_palette']);
               $data['RoleAdmin_text']           = HTMLForm::clean($_POST['Admin_color']);
               $data['RoleModerator_text']       = HTMLForm::clean($_POST['Mod_color']);
               $data['RoleGuest_text']           = HTMLForm::clean($_POST['Guest_color']);
        flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
               
       }else{
            if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
           $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, '
           <div class="row">
           <div class="col">
           '.HTMLForm::checkBox('state', $data[$plugin.'state']).'
           </div>
           </div>
           <div class="row">
           <div class="col">
           '.HTMLForm::text('Admin_label',$data['RoleAdmin_label']).'
           </div>
            <div class="col">
           '.HTMLForm::text('Mod_label',$data['RoleModerator_label']).'
           </div>
            <div class="col">
           '.HTMLForm::text('Guest_label',$data['RoleGuest_label']).'
           </div>
           </div>
           <div class="row">
           <div class="col">
           '.HTMLForm::text('Admin_palette', $data['RoleAdmin_btn'], 'text', 'color').'
           </div>
            <div class="col">
           '.HTMLForm::text('Mod_palette', $data['RoleModerator_btn'], 'text', 'color').'
           </div>
            <div class="col">
           '.HTMLForm::text('Guest_palette', $data['RoleGuest_btn'], 'text', 'color').'
           </div>
           </div>
             <div class="row">
           <div class="col">
           '.HTMLForm::text('Admin_color', $data['RoleAdmin_text'], 'text', 'color').'
           </div>
            <div class="col">
           '.HTMLForm::text('Mod_color', $data['RoleModerator_text'], 'text', 'color').'
           </div>
            <div class="col">
           '.HTMLForm::text('Guest_color', $data['RoleGuest_text'], 'text', 'color').'
           </div>
           </div>
           '.HTMLForm::simple_submit());
       }
       return $out;
    }
}
function RoleTag_footerJS(){
      global $lang, $sessionTrip;
	$plugin = 'RoleTag';
    $out = '';
      if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
        if($data[$plugin.'state']){
            		$itemEntry['id'] 		= flatDB::newEntry();
		            $id 					= $itemEntry['id'];
           // $trip = HTMLForm::trip(HTMLForm::clean(Parser::translitIt($sessionTrip)), $id);
            $out .= '<script>
            var RoleTagOptions = \'{"Admin":{"label":"'.$data['RoleAdmin_label'].'","btn_color": "'.$data['RoleAdmin_btn'].'","txt_color": "'.$data['RoleAdmin_text'].'"},"Moderator":{"label":"'.$data['RoleModerator_label'].'","btn_color": "'.$data['RoleModerator_btn'].'","txt_color": "'.$data['RoleModerator_text'].'"},"Guest":{"label":"'.$data['RoleGuest_label'].'","btn_color": "'.$data['RoleGuest_btn'].'","txt_color": "'.$data['RoleGuest_text'].'"}}\';
            
            </script>';
            $out .= '<script src="'.HTML_PLUGIN_DIR.$plugin.DS.'js'.DS.'RoleTag.js?v=1.1.0"></script>';
            return $out;
        }
}
?>