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

define('PATH_PROFILE_AVATAR', UPLOADS_DIR . 'avatars' . DS);

function Profile_install()
{
	global $lang;
	$plugin = 'Profile';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	$data['menu']   			  	= 'Profile';  
	$data[$plugin.'display_menu'] 	= true;  
    $data['accept']                 = 'png';
	flatDB::saveEntry('plugin', $plugin, $data);
}
/**
 * Admin
**/
function Profile_config()
{    
	   global $lang, $token; 
       $plugin = 'Profile';
       $out ='';
     
       if(!empty($_POST) && CSRF::check($token) )
       {
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['menu'] 		 = HTMLForm::clean($_POST['menu']); 
               $data[$plugin.'display_menu'] = isset($_POST['display_menu'])? $_POST['display_menu'] : '';    
               $data['accept']       = HTMLForm::clean($_POST['accept']);
               flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }
        else
       {
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
                    <div class="col">
                    File Accept(.png is deafult and only 1 file type/logo)'.
                    HTMLForm::text('accept', isset($data)? $data['accept'] : ''). '
                    </div>
                </div>'.      
               HTMLForm::simple_submit());
       }
       return $out;
}

function Profile_menu()
{
  $plugin = 'Profile';
  global $lang, $cur;
  $out ='';
  # Lecture des données
  $data = flatDB::readEntry('plugin', $plugin);
  if ($data[$plugin.'state'] && $data[$plugin.'display_menu']) 
    $out .= '<li class="list-inline-item"><a class="nav-link' .($cur==$plugin ? ' active' : ''). '" href="view.php' . DS . 'plugin' . DS .$plugin. '"><i class="fas fa-user" aria-hidden="true"></i> ' .$data['menu']. '</a></li>';
  
  return $out;  
}

/**
 * Page du plugin
**/
function Profile_view()
{
  global $lang, $sessionTrip;	
  $plugin = 'Profile';
  $out ='';	  	  
  # Lecture des données
  $data = flatDB::readEntry('plugin', $plugin);

  if($data[$plugin.'state']){
      if(!$_SESSION['trip']){
          $disable = "disabled='true'";
          $hide = "";
      }else{
          $disable = "";
          $hide = "hidden='true'";
      }
      if(isset($_COOKIE['flatboard_trip'])){
          $user = $_COOKIE['flatboard_trip'];
      }else{
          $user = "Cookie is not defined";
      }
    	$itemEntry['id'] 		= flatDB::newEntry();
		$id 					= $itemEntry['id'];
		$itemEntry['trip'] 		= $sessionTrip ? HTMLForm::trip(HTMLForm::clean(Parser::translitIt($sessionTrip)), $id) : HTMLForm::trip(HTMLForm::clean(Parser::translitIt($_POST['trip'])), $id);
    $str = strpos($itemEntry['trip'],  '@');
    $getCode = substr($itemEntry['trip'] , $str+1);
    $getUser = substr($itemEntry['trip'] , -$str-$str, $str);
    //pattern='^[a-zA-Z0-9_.+-]+@+[a-zA-Z0-9]+$' {sytax pattern}
      $out.="<style>.profileCon{background-color:gray;color:white;}.profileCon input[type='text']{width:50%;transition:all 0.5s;}.profileCon input[type='text']:focus{border:4px solid rgba(66, 245, 230, 0.5);width:100%;}.profileCon input[type=submit]{width:100%;border-radius:25px;background-color:blue;color:white;}.profileCon p{text-align:center;font-size:32px; color:lightgray;}</style><div class='con profileCon'>
  <form method='post' enctype='multipart/form-data'>
  <p ".$hide .">".$lang['loggin']."</p>
  <label>".$lang['label_user_psw']."</label><br/><input class='form-control' type='text' ".$disable." readonly placeholder='Enter Encrypted Password' value='".$getCode."' name='psedoProfile' required='' title='".$lang['user_err']."'/>
  <br/><br/>
  <label>".$lang['img_uplod_label']."</label>&nbsp;<input type='file' ".$disable." required='' name='imgFiles' style='width:100%;' accept='".$data['accept']."'/>
  <br/><br/>
    <label>".$lang['label_username']."</label>&nbsp;<br/><input class='form-control' type='text' ".$disable." name='customUsername' placeholder='Enter custom username'/>
  <br/><br/>
  <input type='submit' name='sbtbtn' ".$disable." value='".$lang['save_profile']."'/>
  </form>
  </div>";
  }
  if(isset($_POST['sbtbtn'])){
  $username = $getUser . "_" . $_POST['psedoProfile'];
  $title = $getUser . "@" . $_POST['psedoProfile'];
  $customUser = $_POST['customUsername'];
  //username
  $tags = flatDB::readEntry('config', 'tags');
  $tagEntry['title'] = $title;
		$tagEntry['replace'] = '&lt;span class=&quot;badge badge-success&quot; data-toggle=&quot;tooltip&quot; data-placement=&quot;top&quot; title=&quot;User checked&quot;&gt;&lt;i class=&quot;fa fa-certificate&quot;&gt;&lt;/i&gt;&lt;/span&gt; '.$customUser;
		$tagEntry['hidden_tag'] = 'on';
		$tagEntry['id'] = flatDB::newEntry();
		$id = $tagEntry['id'];
		
		$tags[$id] = $tagEntry;
		flatDB::saveEntry('config', 'tags', $tags);
  //icons
  $target = PATH_PROFILE_AVATAR;
  $file_tar  = $target . basename($_FILES['imgFiles']['name']);
  $out .= $file_tar;
  if(move_uploaded_file($_FILES['imgFiles']['tmp_name'], $file_tar)){

       $fileType = explode('.',$_FILES['imgFiles']['name']);

      $out .= '<div class="con">File have been uploaded</div>';
      if(file_exists($target . $username.".".$fileType[1])){
          if(unlink($target . $username.".".$fileType[1])){
              $out .= "Removed oringial icon";
          }else{
              $out .= "Cannot remove oringial icon";
          }
      }
      if(rename($file_tar, $target . $username.".".$fileType[1])){
          $out .= '<div class="con">File has been renamed</div>';
          
      }else{
          $out .= '<div class="con">Sorry could not rename upload file.</div>';
      }

      }
      else{
          $out .= '<div class="con">Sorry could not upload file. AUTH::'.$file_tar.'</div>';
          }
  }

      

  return $out;  
}
function Profile_footer(){
    $script .= "<script>let img = document.querySelectorAll('.rounded-circle');for(i=0;i<img.length;i++){img[i].src = img[i].src}</script>";

    return $script;
}


?>