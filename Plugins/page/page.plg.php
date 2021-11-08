<?php defined('FLATBOARD') or die('Flatboard Community.');
/**
 * profile
 *
 * @author 		SurveyBuilder-Admin.
 * @copyright	(c) 2015-2021
 * @license		http://opensource.org/licenses/MIT
 * @package		FlatBoard
 * @version		1.0
 * @update		2021-09-22
 */	
 
/**
 * On pré-installe les paramètres par défauts.
**/

require("views/view.lib.php");

function page_install()
{
	global $lang;
	$plugin = 'page';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	$data['menu']   			  	= 'Pages';  
	$data[$plugin.'display_menu'] 	= true;  
    $data['pages']                  = '';
	flatDB::saveEntry('plugin', $plugin, $data);
}
function page_config()
{    
	   global $lang, $token; 
       $plugin = 'page';
       $out ='';
     
     if(User::isAdmin()){
           if(!empty($_POST) && CSRF::check($token)){
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['menu'] 		 = HTMLForm::clean($_POST['menu']); 
               $data[$plugin.'display_menu'] = isset($_POST['display_menu'])? $_POST['display_menu'] : '';    
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
				    <div class="col">
                    Display_Menu:'. HTMLForm::checkBox('display_menu', isset($data)? $data[$plugin.'display_menu'] : ''). '
				    </div>
                    
				</div>'.  
                '<div class="row">
                <div class="col">'.
				    HTMLForm::text('menu', isset($data)? $data['menu'] : ''). '
				    </div>
                </div>'.   
                '<div class="row">
                <div class="col">
                <div class="alert alert-info" role="alert">
                        If you need to create a page go to <u>plugins/page/p/create-file.php</u>
                    </div>
                </div>
                </div>'  .
               HTMLForm::simple_submit());

    }
    return $out;
     }
     
}


function page_menu()
{
  $plugin = 'page';
  global $lang, $cur;
  $out ='';
  # Lecture des données
  $data = flatDB::readEntry('plugin', $plugin);

$path    = PLUGIN_DIR . "page". DS . "p";
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));
$list = '';
  if ($data[$plugin.'state'] && $data[$plugin.'display_menu']) 
  foreach($files as $file){
                $list .= '<a class="dropdown-item" href="view.php'.DS.'plugin'.DS.$plugin.DS.'p'.DS.str_replace(".php", "", $file).'">'.str_replace(".php", "", $file).'</a>';
            }
    $out .= '<li class="list-inline-item">
    <div class="dropdown">

    <a class="nav-link'.($cur==$plugin ? ' active' : ''). ' dropdown-toggle"  id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button" href="/forum/#'.'"><i class="fas fa-file-alt" aria-hidden="true"></i> ' .$data['menu']. '</a>

     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            '.
             $list
            .'
  </div>

    </div>
    </li>';
    
  
  return $out;  
}
function page_view()
{
  global $lang;	
  $plugin = 'page';
  $out ='';	  	  
  $data = flatDB::readEntry('plugin', $plugin);
  $path    = PLUGIN_DIR . "page". DS . "p";
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));
$list = '';
  if ($data[$plugin.'state'] && $data[$plugin.'display_menu']) 

    if(!file_exists(PLUGIN_DIR."page".DS."views".DS."view.".basename($_SERVER['REQUEST_URI']).".json")){
        $file = fopen(PLUGIN_DIR."page".DS."views".DS."view.".basename($_SERVER['REQUEST_URI']).".json", "w+");
        $getView = '
        {
            "views": 1
        }
        ';
        fwrite($file, $getView);
        fclose($file);
    }else{
       $getContent = file_get_contents(PLUGIN_DIR."page".DS."views".DS."view.".basename($_SERVER['REQUEST_URI']).".json");
        $query = json_decode($getContent);
        $addView = floatval($query->views) + 1;
         $file = fopen(PLUGIN_DIR."page".DS."views".DS."view.".basename($_SERVER['REQUEST_URI']).".json", "w+");
  
        $getView = '
        {
            "views": '. $addView.'
        }
        ';
        fwrite($file, $getView);
        fclose($file);
    }
    $grabView = new getViews();
    $d = $grabView->toCalc($query->views);
    $out .= $grabView->ConvertToBadge($d);
       $out .= file_get_contents(PLUGIN_DIR."page".DS."p".DS.basename($_SERVER['REQUEST_URI'].".php"));
   
   
return $out;

  }

  function page_footerJS(){
       $out.='<script>
    setTimeout(function(){
            document.querySelector(".lead").style.display = "none";
    }, 0);
    </script>';
    return $out;
  }
