<?php defined("FLATBOARD") or die('Flatboard Community.');

function Mention_install(){
    global $lang;
    $plugin = 'Mention';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;
  $data[$plugin.'state']   	  	= true;  
  	flatDB::saveEntry('plugin', $plugin, $data);
}
function Mention_config(){
 global $lang, $token;
    $plugin = 'Mention';
         if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               $out = '';
            if(User::isAdmin()){
                 if(!empty($_POST) && CSRF::check($token) )
                    {
            $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
            }else{
                  if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
                  $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, 
               '<div class="row">
				    <div class="col">'.
				    HTMLForm::checkBox('state', $data[$plugin.'state']). '
				    </div>
                 </div>'.HTMLForm::simple_submit());
            }
            return $out;
        }
}
function Mention_footerJS(){
        $plugin = 'Mention';
    $out = '';
      if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               $out = '';
            if($data[$plugin.'state']){
                     $out .= '<script src="'.HTML_PLUGIN_DIR.$plugin.DS.'js'.DS.'mention.js?v=1.0.7'.'"></script>';
            }else{
                $out .= '';
            }
            return $out;
}
?>