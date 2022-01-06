<?php defined('FLATBOARD') or die('Flatboard Community.');
function BackupAdminKey_install(){
    	global $lang, $sessionTrip;
	$plugin = 'BackupAdminKey';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= true; 
    $data[$plugin.'user']          = $sessionTrip;
    $data['hash']                 = '';
	flatDB::saveEntry('plugin', $plugin, $data);
}
function BackupAdminKey_config(){
        
    global $lang, $token, $sessionTrip; 
       $plugin = 'BackupAdminKey';
       $out ='';
if(User::isAdmin()){
     if(!empty($_POST) && CSRF::check($token) )
       {
            $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
           $getTrip = $_POST['keyHidden'];
           $hash = hash("md5", $getTrip);
           $hash = hash("sha384", $hash);
           $hash = hash("crc32b", $hash);
           $hash = hash("whirlpool",$hash);
           $hash = str_shuffle($hash);
           $data['hash'] = $hash;
           $code = '{"hash":"'.$hash.'","user":"'.$getTrip.'"}';
           $f = fopen(PLUGIN_DIR.$plugin.DS.'key/KEY_USER.json', "w+");
           fwrite($f, $code);
           fclose($f);
      flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }else{
            if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               if($data['hash'] === ""){
                   $hidden = "hidden='true'";
               }else{
                   $hidden = "";
               }
               $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, 
               '<div class="row">
				    <div class="col">'.
				    HTMLForm::checkBox('state', $data[$plugin.'state']). '
				    </div>
                 </div>
                 <div class="row">
                 <div class="col">
                 <input type="text" value="'.$sessionTrip.'" name="keyHidden" placeholder="Pseudo" required="" class="form-control"/>
                 </div>
                 <div class="row" '.$hidden.'>
                 <div class="col">
                 You key is: '.$data['hash'].'
                 </div>
                 </div>
                 <br/><br/>
                 </div>'.HTMLForm::simple_submit()
               );
       }
    }
    return $out;
}

function BackupAdminKey_footerJS(){
    $plugin = 'BackupAdminKey';
    $out = '';
      if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               if($data[$plugin.'state']){
                    $out .= '<script src="'.HTML_PLUGIN_DIR.$plugin.DS.'BackUpAdminKey.js?v=1.1.0'.'"></script>';
               }else{
                   $out .= '';
               }
    return $out;
}
?>
