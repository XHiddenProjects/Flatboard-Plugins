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
include(PLUGIN_DIR . $plugin . DS. "createFileSession.php");
//plugins
 require(PLUGIN_DIR . $plugin . DS. "plugins".DS."alertUI".DS."alertUI.plg.php");

//contruct
require(PLUGIN_DIR . $plugin . DS. "lib".DS."bannedUsers.lib.php");

function bannedUsers_install()
{
	global $lang, $sessionTrip;
	$plugin = 'bannedUsers';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= true; 
    $data['username']               = '';
    $data['isBanned']               = false;
    $data['bannedMessage']           = '';
    $data['ip']                     = '';
    $data['appeal']                 = 'appeal/';
    $adminGetter = explode("@", $sessionTrip);
    $data['isAdmin']        =  $adminGetter[0];
    
	flatDB::saveEntry('plugin', $plugin, $data);
}

function bannedUsers_config()
{
 global $lang, $token, $sessionTrip, $imgs, $vids, $replace;
 $plugin = "bannedUsers";
 $out = '';
 $bannedLibs = new bannedUsers();
 if(User::isAdmin()){
      if(!empty($_POST) && CSRF::check($token) )
       {
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['username'] 		 = HTMLForm::clean($_POST['userLabel']); 
               $data['isBanned'] 		 = HTMLForm::clean($_POST['BannedCheck']);
               $data['bannedMessage']    = HTMLForm::clean($_POST['BanTxtarea']);
               $data['appealURI']        = HTMLForm::clean($_POST['appealURI']);
               $adminGetter = explode("@", $sessionTrip);
                $data['isAdmin']        =  $adminGetter[0];
           
               
               $db = PLUGIN_DIR . $plugin . DS. "db". DS .$data['username'].".dat.json";
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
             
             
              $image = scandir(PLUGIN_DIR.$plugin.DS."assets/imgs");
              foreach($image as $img){
                  if(!preg_match("/\.(gif|jpe?g|tiff?|png|webp|bmp)$/i", $img)){
                      $imgs = htmlspecialchars('<div class="alert alert-danger" role="alert">Error: '.$img.' is not a valid image type</div>', ENT_QUOTES);
                  }else{
                       $id = uniqid();
                  $imgs = htmlspecialchars('<img src="'.HTML_PLUGIN_DIR.$plugin.DS."assets/imgs".DS.$img.'" width="320" height="320" alt="'.$id.'" title="image-'.$id.'"/>', ENT_QUOTES);
                  }
                 
              }
    //videos
              $video = scandir(PLUGIN_DIR.$plugin.DS."assets/vids");
              foreach($video as $vid){
                  if(!preg_match("/\.(mp(eg)?4|mov|wmv)$/i", $vid)){
                      $vids = htmlspecialchars('<div class="alert alert-danger" role="alert">Error: '.$vid.' is not a valid video type</div>', ENT_QUOTES);
                  }else{
                       $id = uniqid();
                  $vids = htmlspecialchars('<video src="'.HTML_PLUGIN_DIR.$plugin.DS."assets/vids".DS.$vid.'" width="320" height="320" alt="'.$id.'" title="image-'.$id.'"></video>', ENT_QUOTES);
                  }
                 
              }
            
         

              
function getBrowser() { 
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version= "";
  $device = "Unknown";
  
     
  
  //First get the platform?
  if (preg_match('/linux/i', $u_agent)) {
    $platform = 'linux';
  }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    $platform = 'mac';
  }elseif (preg_match('/windows|win32/i', $u_agent)) {
    $platform = 'windows';
  }

  // Next get the name of the useragent yes seperately and for good reason
  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
    $bname = 'Internet Explorer';
    $ub = "MSIE";
  }elseif(preg_match('/Firefox/i',$u_agent)){
    $bname = 'Mozilla Firefox';
    $ub = "Firefox";
  }elseif(preg_match('/OPR/i',$u_agent)){
    $bname = 'Opera';
    $ub = "Opera";
  }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
    $bname = 'Google Chrome';
    $ub = "Chrome";
  }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
    $bname = 'Apple Safari';
    $ub = "Safari";
  }elseif(preg_match('/Netscape/i',$u_agent)){
    $bname = 'Netscape';
    $ub = "Netscape";
  }elseif(preg_match('/Edge/i',$u_agent)){
    $bname = 'Edge';
    $ub = "Edge";
  }elseif(preg_match('/Trident/i',$u_agent)){
    $bname = 'Internet Explorer';
    $ub = "MSIE";
  }

  // finally get the correct version number
  $known = array('Version', $ub, 'other');
  $pattern = '#(?<browser>' . join('|', $known) .
')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
  if (!preg_match_all($pattern, $u_agent, $matches)) {
    // we have no matching number just continue
  }
  // see how many we have
  $i = count($matches['browser']);
  if ($i != 1) {
    //we will have two since we are not using 'other' argument yet
    //see if version is before or after the name
    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
        $version= $matches['version'][0];
    }else {
        $version= $matches['version'][1];
    }
  }else {
    $version= $matches['version'][0];
  }

  // check if we have a number
  if ($version==null || $version=="") {$version="?";}

  return array(
    'userAgent' => $u_agent,
    'name'      => $bname,
    'version'   => $version,
    'platform'  => $platform,
    'pattern'    => $pattern
  );
} 

$getAgent = getBrowser();

               
             
               
               
            
               $data['bannedMessage'] = str_replace("{{GET_ADMIN}}", $data['isAdmin'], $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_USER}}", $data['username'], $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_DATETIME}}", date("m-d-Y h:i:sa"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_DATE}}", date("m-d-Y"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_TIME}}", date("h:i:sa"), $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_IP}}", $data['ip'], $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_BROWSER}}", $getAgent['name'] , $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_USERAGENT}}", $getAgent['userAgent'] , $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_VERSION}}", $getAgent['version'] , $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{GET_PLATFORM}}", $getAgent['platform'] , $data['bannedMessage']);
             
                $data['bannedMessage'] = str_replace("{{GET_APPEAL_LINK}}", $url, $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{POST_IMAGES}}", $imgs, $data['bannedMessage']);
                $data['bannedMessage'] = str_replace("{{POST_VIDEOS}", $vids, $data['bannedMessage']);

                $dom = PLUGIN_DIR . $plugin . DS. "db". DS . $data['username'].".dat.json";
                if(file_exists($dom)){
                              $contents = file_get_contents($dom);
                $deco = json_decode($contents);
                }
                if(!file_exists($dom)){
                $createFile = fopen($dom, "w+");
                $data['bannedMessage'] = str_replace(array("\r","\n"), "", $data['bannedMessage']);
               echo "created";
                    fwrite($createFile, '{"username":"'.$data['username'].'", "isBanned":"'.$data['isBanned'].'", "bannedMessage": "'.$data['bannedMessage'].'",                "ip":"'.$data['ip'].'", "status": "pending", "timeStamp":"'.date('Y-m-d').'"}');
                    fclose($createFile);
                }else{
                     $createFile = fopen($dom, "w+");
                    $data['bannedMessage'] = str_replace(array("\r","\n"), "", $data['bannedMessage']);
           echo "updated";
                    fwrite($createFile, '{"username":"'.$data['username'].'", "isBanned":"'.$data['isBanned'].'", "bannedMessage": "'.$data['bannedMessage'].'",                "ip":"'.$data['ip'].'", "status": "'.$deco->status.'", "timeStamp":"'.date('Y-m-d').'"}');
                    fclose($createFile);
                }
               
                
                    


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

                
         
    
       $out.="<span id='panelbtn' onclick='showDisplayer()' data-toggle='tooltip' data-placement='top' title='".$lang['panelOpen']."'>".$lang['panelOpen']."</span>";
       $out.="<span id='displayCon'><div class='closebtn' onclick='hideDisplayer()' data-toggle='tooltip' data-placement='top' title='".$lang['panelClose']."'><i class='fas fa-times-circle'></i> ".$lang['panelClose']."</div>";
 $getDom = PLUGIN_DIR . $plugin . DS. "db". DS;
                    $files = glob($getDom."*.dat.json");
                   

                  
                    foreach($files as $file){
                      
                     $replace = str_replace($getDom,"",$file);
                    
                       
                    if($bannedLibs->isFailed($replace)){
                $out.= "<div class='alert alert-danger' role='alert'><i class='fas fa-times-circle'></i> Failed user... ".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?delete=".$replace."'><i class='fas fa-user-times title='Delete User' style='color:red;'></i></a> | <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?repend=".$replace."'><i class='fas fa-sync-alt' title='Repend' style='color:blue;'></i></a></div>"; 
                    }else{
       
                        if($bannedLibs->isPending($replace)){
                            if(!$bannedLibs->checkPending($replace)){
                                    $plugin = 'bannedUsers';
 $dom = PLUGIN_DIR . $plugin . DS. "db". DS . $replace;
           $createFile = fopen($dom, "w+");
                    fwrite($createFile, '{"username":"'.$replace.'", "isBanned":"", "bannedMessage": "", "ip": "", "status": "failed", "timeStamp": ""}');
                    fclose($createFile);
                       $out.= "<div class='alert alert-danger' role='alert'><i class='fas fa-times-circle'></i> ".$lang['pendFail']." ".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?delete=".$replace."'><i class='fas fa-user-times' title='Delete User' style='color:red;'></i></a> | <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?repend=".$replace."'><i class='fas fa-sync-alt' title='Repend' style='color:blue;'></i></a></div>"; 
                            }else{
                                $getPend = $bannedLibs->pendingExpire($replace);
                               if($getPend <= 3){
                                     $ui = new alertUI();
                                    $out.= $ui->create('halfPoint',  $ui->replace(".dat.json","",$replace) . " have ". $getPend . " until failure"); 
                                }
                        $out.= "<div class='alert alert-warning' role='alert'><i class='fas fa-exclamation-triangle'></i> ".$lang['pend']  ." ".$replace." ".$lang['pendExpire']  ." ".$getPend." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?delete=".$replace."'><i class='fas fa-user-times' title='Cancel Pending' style='color:red;'></i></a></div>"; 
                            }
                    
                        }else{
                            if(!$bannedLibs->CheckBanned($replace)){
                           $out.= "<div class='alert alert-success' role='alert'><i class='fas fa-check-circle'></i> ".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?ban=".$replace."'><i class='fas fa-user-unlock' title='Ban' style='color:green;'></i></a></div>"; 
                        }else{
$out.= "<div class='alert alert-danger' role='alert'><i class='fas fa-times-circle'></i> ".$replace." <a href='".str_replace($_SERVER["DOCUMENT_ROOT"],"",$getDom)."status.php?unban=".$replace."'><i class='fas fa-user-lock' title='Unban' style='color:red;'></i></a></div>"; 
                            }
                        }
              
                    }

                        
                    

                    

                    }

             

            
                       
                    }
                        
                        
        $out.="</span>";
                    
    
       return $out;

    }

 function bannedUsers_head(){
     global $sessionTrip;
       $plugin = 'bannedUsers';
     $out = '';
     $data = flatDB::readEntry('plugin', $plugin);
     if($data[$plugin.'state']){
         $out .= "<link rel='stylesheet' href='https://proicons.netlify.app/css/icons.min.css'/>";
         $out .= "<style>.alert-success{
             background-color:#38ff66 !important;
             border-color:#b4e0be !important;
         }
         .closebtn{
             background-color:gray;color:red;font-size:30px;cursor:pointer;
         }
         #panelbtn{
             position:absolute;bottom:0;left:4%;font-size:20px;background-color:#343A40;cursor:pointer;
         }
         #displayCon{
             position:absolute;top:40%;left:50%;transform:translate(-40%,-50%);background-color:cyan;color:black;display:block;width:60%;height:45%;overflow:auto;font-size:25px;display:none;
         }
         
         </style>";
          $ui = new alertUI();
         $out .= $ui->style();
         if($sessionTrip){
             CreateUser($sessionTrip,'','', User::getRealIpAddr(), 'active', date('Y-m-d'));
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
         $out.="<script>
        function hideDisplayer(){
             document.querySelector('#displayCon').style.display = 'none';
             document.querySelector('#panelbtn').style.display = 'block';
         }
         function showDisplayer(){
              document.querySelector('#displayCon').style.display = 'block';
             document.querySelector('#panelbtn').style.display = 'none';
         }
         
         </script>";
         if($sessionTrip){ 
             $getDom =PLUGIN_DIR . $plugin . DS. "db". DS;
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
