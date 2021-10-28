<?php defined('FLATBOARD') or die('Flatboard Community.');

/**
 * CustomStyle
 *
 * @author 		SurveyBuilder-Admin.
 * @copyright	(c) 2015-2021
 * @license		http://opensource.org/licenses/MIT
 * @package		FlatBoard
 * @version		1.0.1
 * @update		2021-10-25
 */	
 
/**
 * We pre-install the default settings.
**/
include(str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "createFileSession.php");
require(str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "lib".DS."bannedUsers.lib.php");
function bannedUsers_install()
{
	global $lang;
	$plugin = 'bannedUsers';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= true; 
    $data['username']               = '';
    $data['isBanned']               = false;
    $data['bannedMessage']           = '';
    $data['ip']                     = '';
    $data['appeal']                 = 'appeal/';
    
	flatDB::saveEntry('plugin', $plugin, $data);
}

function bannedUsers_config()
{
 global $lang, $token, $sessionTrip, $imgs, $vids; 
 $plugin = "bannedUsers";
 $out = '';
 if(User::isAdmin()){
      if(!empty($_POST) && CSRF::check($token) )
       {
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['username'] 		 = HTMLForm::clean($_POST['userLabel']); 
               $data['isBanned'] 		 = HTMLForm::clean($_POST['BannedCheck']);
               $data['bannedMessage']    = HTMLForm::clean($_POST['BanTxtarea']);
               $data['appealURI']        = HTMLForm::clean($_POST['appealURI']);
               $db = str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "db". DS .$data['username'].".dat.json";
              if(file_exists($db)){
                  $getContent = file_get_contents($db);
                  $query = json_decode($getContent);
                  if($query->ip === ""){
                     $data['ip'] = User::getRealIpAddr();
                  }else{
                      $data['ip'] = $query->ip;
                  }
                  
              }
               //keywords
             
              if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
                  $sec = "https://";
              }else{
                  $sec = "http://";
              }
              $url = $sec.$_SERVER['HTTP_HOST']."/".$data['appealURI'];
             
             
              $image = scandir(PLUGIN_DIR.$plugin.DS."assets/img");
              foreach($image as $img){
                  if(!preg_match("/\.(gif|jpe?g|tiff?|png|webp|bmp)$/i", $img)){
                      $imgs = htmlspecialchars('<div class="alert alert-danger" role="alert">Error: '.$img.' is not a valid image type</div>', ENT_QUOTES);
                  }else{
                       $id = uniqid();
                  $imgs = htmlspecialchars('<img src="'.HTML_PLUGIN_DIR.$plugin.DS."img".DS.$img.'" width="320" height="320" alt="'.$id.'" title="image-'.$id.'"/>', ENT_QUOTES);
                  }
                 
              }
    //videos
              $video = scandir(PLUGIN_DIR.$plugin.DS."assets/vids");
              foreach($video as $vid){
                  if(!preg_match("/\.(mp(eg)?4|mov|wmv)$/i", $img)){
                      $vids = htmlspecialchars('<div class="alert alert-danger" role="alert">Error: '.$img.' is not a valid video type</div>', ENT_QUOTES);
                  }else{
                       $id = uniqid();
                  $vids = htmlspecialchars('<video src="'.HTML_PLUGIN_DIR.$plugin.DS."img".DS.$img.'" width="320" height="320" alt="'.$id.'" title="image-'.$id.'"></video>', ENT_QUOTES);
                  }
                 
              }
             
               
               
               $getAdmin = explode("@", $sessionTrip);
               $data['bannedMessage'] = str_replace("{{GET_ADMIN}}", $getAdmin[0] , $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_USER}}", $data['username'], $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_DATETIME}}", date("m-d-Y h:i:sa"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_DATE}}", date("m-d-Y"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_TIME}}", date("h:i:sa"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_IP}}", $data['ip'], $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_BROWSER}}",$_SERVER['HTTP_USER_AGENT'] , $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_APPEAL_LINK}}", $url, $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{POST_IMAGES}}", $imgs, $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{POST_VIDEOS}", $vids, $data['bannedMessage']);

                $dom = str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "db". DS . $data['username'].".dat.json";
                $createFile = fopen($dom, "w+");
                $data['bannedMessage'] = str_replace(array("\r","\n"), "", $data['bannedMessage']);
                    fwrite($createFile, '{"username":"'.$data['username'].'", "isBanned":"'.$data['isBanned'].'", "bannedMessage": "'.$data['bannedMessage'].'", "ip":"'.$data['ip'].'"}');
                    fclose($createFile);
                

                flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
    }else{
      if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
               if(!file_exists(LANG_DIR. "es-ES.php") || file_get_contents(LANG_DIR."es-ES.php") === ""){
                   $span_err ='<div class="alert alert-danger" role="alert">'.$lang['err_missing_spanish'].'</div>';
               }else{
                   $span_err = '';
               }
               $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, 
               '
               <div class="row">
               <div class="col">
               <div class="alert alert-warning" role="alert">'.$lang['alert'].'</u></div>
               '.
              $span_err
               .'
               </div>
               </div>
				<div class="row">
				    <div class="col">'.
				    HTMLForm::checkBox('state', $data[$plugin.'state']). '
				    </div>
                    </div>
                    <div class="row">
                    <div class="col">'.
                       HTMLForm::text('userLabel', '', 'text', '', 'userPlace', '', false)
                    .'</div>
                    </div>'.
                    '<div>'.
                        HTMLForm::text('appealURI', '', 'text', '', 'enterURI', '', false)
                    .'</div>'.
                    '<div class="row">
                    <div class="col">'.
                        HTMLForm::checkBox('BannedCheck', '', '')
                    .'</div>
                    </div>'.
                    '<div class="row">
                    <div class="col">'.
                    '<button type="button" data-toggle="tooltip" data-placement="top" title="Template:default" class="btn btn-primary"'.'onclick=template("default")'.'>Default Template</button><br/>'.
                    HTMLForm::textarea('BanTxtarea', '', '', '', '', 'BanTxtAreaPlace', true)
                    .'</div>
                    </div>'.
				'</div>'.       
               HTMLForm::simple_submit()
               );
       }
       $out.="<ul style='position:absolute;bottom:-10%;background-color:cyan;color:black;width:60%;height:10%;overflow:auto;font-size:25px;'>";
 $getDom = str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "db". DS;
                    $files = glob($getDom."*.dat.json");
                    foreach($files as $file){
                        $replace = str_replace($getDom,"",$file);
                         
                        if(!CheckBanned($replace)){
                             $out.= "<li style='list-style:none;'>".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?ban=".$replace."'><i class='fas fa-user-unlock' title='Ban' style='color:green;'></i></a></li>"; 
                        }else{
$out.= "<li style='list-style:none;'>".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?unban=".$replace."'><i class='fas fa-user-lock' title='Unban' style='color:red;'></i></a></li>"; 
                        }
                        
                        }
        $out.="</ul>";
       return $out;

    }
 }
 function bannedUsers_head(){
     global $sessionTrip;
       $plugin = 'bannedUsers';
     $out = '';
     $data = flatDB::readEntry('plugin', $plugin);
     if($data[$plugin.'state']){
         $out .= "<link rel='stylesheet' href='https://proicons.netlify.app/css/icons.min.css'/>";
         if($sessionTrip){
             CreateUser($sessionTrip,'','',User::getRealIpAddr());
         }
     }
      
                
     return $out;
 }
 function bannedUsers_footerJS(){
     global $sessionTrip;
     $plugin = 'bannedUsers';
     $out = '';
     $data = flatDB::readEntry('plugin', $plugin);
     if($data[$plugin.'state']){
         $out = "<script>document.querySelector('#BannedCheck').addEventListener('click', function(){
if(!document.querySelector('#BannedCheck').checked){document.querySelector('#BanTxtarea').disabled = true;}else{document.querySelector('#BanTxtarea').disabled = false;}
         });</script>";
         $out.='<script>
         function template(t){
             if(t==="default"){
                 $.get("'.HTML_PLUGIN_DIR.$plugin.'/templates/default.html", function(data){
                 document.querySelector("#BanTxtarea").value = data;
             });
             }
             
         }
         </script>';
         if($sessionTrip){ 
             $getDom = str_replace("https://surveybuilder.epizy.com", $_SERVER['DOCUMENT_ROOT'],HTML_PLUGIN_DIR) . $plugin . DS. "db". DS;
             $files = glob($getDom."*.dat.json");
             foreach($files as $file){
                 $getContent = file_get_contents($file);
                 $query = json_decode($getContent);
                 if('"'.$sessionTrip.'"' === '"'.$query->username.'"'){
                         $out .= "<script>
          setTimeout(function(){
              if('".md5(sha1($sessionTrip))."' === '".md5(sha1($query->username))."' && '".$query->isBanned."' !== ''){
                    document.body.innerHTML = '".htmlspecialchars_decode($query->bannedMessage, ENT_QUOTES)."';
           }else{
               //example of terms
           } 
        }, 0)</script>";
                 }
         }

         } else{
            $out .= "<input type='hidden'/>";
         }
             }else{
         "//hello";
     }
     return $out;
 }



?>
