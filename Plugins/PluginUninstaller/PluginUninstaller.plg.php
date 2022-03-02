<?php defined("FLATBOARD") or die("Flatboard community");
function PluginUninstaller_install(){
    global $lang;
	$plugin = 'PluginUninstaller';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	flatDB::saveEntry('plugin', $plugin, $data);
}
function PluginUninstaller_config(){
  global $lang, $token; 
       $plugin = 'PluginUninstaller';
       $out ='';
       if(User::isAdmin()){
             if(!empty($_POST) && CSRF::check($token)){
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : '';   
               flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }else{
           if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
                $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, '
				<div class="row">
				    <div class="col">'.
				    HTMLForm::checkBox('state', $data[$plugin.'state']). '
				    </div>
                    </div>'.HTMLForm::simple_submit());
                if($data[$plugin.'state']){
                    $out .= '<br/><a href="'.str_replace($_SERVER['DOCUMENT_ROOT'],'',PATH_ROOT).DS.'view.php/plugin'.DS.$plugin.'"><button type="button" class="btn btn-danger">Remove Plugins</a>';
                }
       }
       return $out;
       }
}
class Reload{
	private function __contruct(){
		#nothing
	}
	public static function run(){
		
		         if(!$_GET['u']){
            echo "<script>setTimeout(function(){window.open('./panel?u=0', '_self')})</script>";
        }else{
            $getNum = intval($_GET['u']) + 1;
            echo "<script>setTimeout(function(){window.open('./panel?u=".$getNum."', '_self')})</script>";
        }
	}
	
}
function deleteFolder($rootPath)
{   
    foreach(new DirectoryIterator($rootPath) as $fileToDelete)
    {
        if($fileToDelete->isDot()) continue;
        if ($fileToDelete->isFile())
            unlink($fileToDelete->getPathName());
        if ($fileToDelete->isDir())
            deleteFolder($fileToDelete->getPathName());
    }

    rmdir($rootPath);
}
function PluginUninstaller_view(){
      global $lang; 
       $plugin = 'PluginUninstaller';
       $out ='';
           if(isset($_POST[$plugin.'_removePlugin'])){
            $getPlugin = $_POST[$plugin.'_pluginName'];
            deleteFolder(PLUGIN_DIR.$getPlugin);
            unlink(DATA_DIR."plugin".DS.$getPlugin.".dat.php");
        }
      if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
           if($data[$plugin.'state'] && User::isAdmin()){
                    $out .= '<form method="post"><select name="'.$plugin.'_pluginName" class="form-control form-control-lg">';
                    $plugins = array_values(array_diff(scandir(PLUGIN_DIR), ['.', '..']));
                    foreach($plugins as $p){
                        if(preg_match('/.*(\.html?)$/',$p)){
                         # nothing
                        }else if($p !== 'PluginUninstaller'){
                        $out .= '<option value="'.$p.'">'.$p.'</option>';
                        }else{
                        # nothing
                        }
                    }
                    
                    $out .= '</select><br/><br/><button name="'.$plugin.'_removePlugin" onclick="return confirm(\'Are you sure you want to delete this plugin?\');" class="btn btn-danger" type="submit">Remove plugin</button></form>';
                      return $out;
                }

    

}
?>