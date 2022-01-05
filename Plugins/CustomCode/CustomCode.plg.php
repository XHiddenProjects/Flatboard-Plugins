<?php defined('FLATBOARD') or die('Flatboard Community.');
/**
 * CustomStyle
 *
 * @author 		SurveyBuilder-Admin.
 * @copyright	(c) 2015-2021
 * @license		http://opensource.org/licenses/MIT
 * @package		FlatBoard
 * @version		1.0
 * @update		2021-09-26
 */	 
 
/**
 * We pre-install the default settings.
**/

function CustomCode_install()
{
	global $lang;
	$plugin = 'CustomCode';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= true; 
    $data['style']                  = '';
    $data['script']                 = '';
    $data['cantDisable']            = true;

    
	flatDB::saveEntry('plugin', $plugin, $data);
}
function CustomCode_config(){
    
    global $lang, $token; 
       $plugin = 'CustomCode';
       $out ='';
       if(isset($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] === 'on'){
           $secu = "https://".$_SERVER['SERVER_NAME'];
       }else{
           $secu = "http://".$_SERVER['SERVER_NAME'];
       }
        $assets = $_SERVER['DOCUMENT_ROOT'].str_replace($secu, '', HTML_PLUGIN_DIR). $plugin. DS. 'assets' . DS;
if(User::isAdmin()){
     if(!empty($_POST) && CSRF::check($token) )
       {
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['style'] 		 = HTMLForm::clean($_POST['css']); 
               $data['script'] 		 = HTMLForm::clean($_POST['js']);
            if(!file_exists($assets.'custom.css')){
        $open = fopen($assets.'custom.css', 'w+');
        fwrite($open, htmlspecialchars_decode($data['style'], ENT_QUOTES));
        fclose($open);
    }else{
         $open = fopen($assets.'custom.css', 'w+');
        fwrite($open, htmlspecialchars_decode($data['style'], ENT_QUOTES));
        fclose($open);
    }
    if(!file_exists($assets.'custom.js')){
        $open = fopen($assets.'custom.js', 'w+');
        fwrite($open, htmlspecialchars_decode($data['script'], ENT_QUOTES));
        fclose($open);
    }else{
        $open = fopen($assets.'custom.js', 'w+');
        fwrite($open, htmlspecialchars_decode($data['script'], ENT_QUOTES));
        fclose($open);
    }
               flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }else{

       if(file_exists($assets.'custom.css')){
           $getCSS = file_get_contents($assets.'custom.css');
       }
       if(file_exists($assets.'custom.js')){
           $getJS = file_get_contents($assets.'custom.js');
       }

 if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, 
               '
				<div class="row">
				    <div class="col">'.
				    HTMLForm::checkBox('state', $data[$plugin.'state']). '
				    </div>
                    <div class="col">
                    <label>'.$lang['saver'].'</label>
                    </div>
				</div>'.    
                '<div class="row">
                  <div class="col">
                    <label>'.$lang['data_css'].'</lable><br/>
                    '. HTMLForm::textarea('css', $getCSS, '', '', '', '', false)
                    .'<a style="color:cyan;text-decoration:underline;" href="./plugin/CustomCode/assets/custom.css" download="custom.css">Download CSS</a>
                    </div>
                </div>'.  
                '<div class="row">
                  <div class="col">
                    <label>'.$lang['data_js'].'</lable><br/>
                    '. HTMLForm::textarea('js', $getJS, '', '', '', '', false)
                    .'<a style="color:cyan;text-decoration:underline;" href="./plugin/CustomCode/assets/custom.js" download="custom.js">Download JS</a>
                    </div>
                </div>'.  
                '<div class="row">
                  <div class="col">
                   <a style="color:cyan;text-decoration:underline;" href="./plugin/CustomCode/removeAssets.php">Delete Assets</a>
                    </div><br/>
                </div>'. 
               HTMLForm::simple_submit());
       }

       return $out;




}
}

function CustomCode_head(){
            $plugin = 'CustomCode';
	 $data = flatDB::readEntry('plugin', $plugin);
	if($data[$plugin.'state']){
		 $assets =  HTML_PLUGIN_DIR. $plugin. DS. 'assets' . DS;
    $out = "<link rel='stylesheet' href='".$assets.'custom.css?id='.uniqid()."' type='text/css'/>";
	}
   
        
        
    
    
    return $out;
}
function CustomCode_footerJS(){
   
        $plugin = 'CustomCode';
	 $data = flatDB::readEntry('plugin', $plugin);
	if($data[$plugin.'state']){
		   $assets = HTML_PLUGIN_DIR . $plugin. DS. 'assets' . DS;
    $out .= '<script src="'.$assets.'custom.js?id='.uniqid().'" type="text/javascript"></script>';
  
	}
 
    return $out;
}


?>
