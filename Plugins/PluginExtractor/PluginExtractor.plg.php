<?php
function PluginExtractor_install()
{
	global $lang;
	$plugin = 'PluginExtractor';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	flatDB::saveEntry('plugin', $plugin, $data);
}
function PluginExtractor_config(){
     global $lang, $token; 
       $plugin = 'PluginExtractor';
       $out ='';
        if(User::isAdmin()){
 if(!empty($_POST) && CSRF::check($token)){
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : '';   
               flatDB::saveEntry('plugin', $plugin, $data);
               $target_dir = PLUGIN_DIR.$plugin.DS."uploads".DS;
               $target_file = $target_dir . basename($_FILES["unziparchive"]["name"]);
               if(move_uploaded_file($_FILES['unziparchive']['tmp_name'], $target_file)){
                $zip = new ZipArchive;
               $res = $zip->open($target_file);
               if ($res === TRUE) {
                    $zip->extractTo(PLUGIN_DIR);
                $zip->close();
                $out .= '<div class="alert alert-success" role="alert">Successfully added '.$_FILES['unziparchive']['name'].'!</div>';
               }else{
                   $out .= '<div class="alert alert-danger" role="alert">Failed to open file!</div>';
               }
               }else{
                   $out .= '<div class="alert alert-danger" role="alert">Failed to upload file!</div>';
               }

               
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }else{
           if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
            $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, '<div class="row">
            <div class="col">'.
              HTMLForm::checkBox('state', $data[$plugin.'state']).
            '</div>
            </div>
            <div class="row">
            <div class="col">
            <label for="ziparcive" class="form-label">Upload zip only</label>
              <input class="form-control" name="unziparchive" id="ziparcive" type="file" accept=".zip" required/>
              </div>
            </div><br/>'.HTMLForm::simple_submit());
     }
     return $out;
}
}
function PluginExtractor_footerJS(){
    $out='';
    $out .= '<script>
    setTimeout(function(){
        document.getElementById("form").enctype = "multipart/form-data";
        },0);
        </script>';
return $out;
}
?>
