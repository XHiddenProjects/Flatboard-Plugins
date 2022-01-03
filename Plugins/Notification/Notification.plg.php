<?php defined('FLATBOARD') or die('Flatboard Community.');
function Notification_install()
{
	global $lang, $sessionTrip;
	$plugin = 'Notification';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state'] 	= true; 
    
	flatDB::saveEntry('plugin', $plugin, $data);
}
function Notification_config(){
     global $lang, $token;
      $plugin = "Notification";
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
           $out .= HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin,'
			<div class="row">
			    <div class="col">'.
			    	HTMLForm::checkBox('state', $data[$plugin.'state']).
			    '</div>
			</div>'.HTMLForm::simple_submit());
       }
       return $out;
    }
}

function Notification_menu(){
  $plugin = 'Notification';
  global $lang, $cur, $sessionTrip, $selTop, $selRep;
    $data = flatDB::readEntry('plugin', $plugin);
    if($data[$plugin.'state']){
  $img = UPLOADS_DIR."avatars".DS;
  $replys = DATA_DIR."reply".DS;
  $topics = DATA_DIR."topic".DS;
  $reply = array_values(array_diff(scandir($replys), array('..', '.')));
 $topic = array_values(array_diff(scandir($topics), array('..', '.')));


 $db_reply = PLUGIN_DIR.$plugin.DS."db".DS."reply".DS;
 $db_topic = PLUGIN_DIR.$plugin.DS."db".DS."topic".DS;
 $db_user = PLUGIN_DIR.$plugin.DS."db".DS."userList".DS;
 
 $remote = str_replace("@","_",$sessionTrip);
 if(!file_exists($db_user.$remote.DS)){
   mkdir($db_user.$remote, 0777);
 }
 if(!file_exists($db_user.$remote.DS."readList".DS)){
      mkdir($db_user.$remote.DS."readList", 0777);
 }
 if(!file_exists($db_user.$remote.DS."stared".DS)){
      mkdir($db_user.$remote.DS."stared", 0777);
 }
  if(!file_exists($db_user.$remote.DS."hidden".DS)){
      mkdir($db_user.$remote.DS."hidden", 0777);
 }

    $db_read = $db_user.$remote.DS."readList".DS;
    $db_star = $db_user.$remote.DS."stared".DS;
    $db_hide = $db_user.$remote.DS."hidden".DS;

 $isRead = array_values(array_diff(scandir($db_read), array('..', '.')));
 $isStared = array_values(array_diff(scandir($db_star), array('..', '.')));
 $isHidden = array_values(array_diff(scandir($db_hide), array('..', '.')));
foreach($reply as $r){
if(!file_exists($replys.$r)){
     if(!unlink($db_read.$r));
   if(!unlink($db_star.$r)); 
   if(!unlink($db_reply.$r));
}
}
foreach($topic as $t){
if(!file_exists($topics.$t)){
    if(!unlink($db_read.$t));
   if(!unlink($db_star.$t));
   if(!unlink($db_topic.$t));
}
}

$new=0;
$old=0;
$total=0;
if(count($isRead) === 0 || !count($isRead)){
$new = $new+count($reply)+count($topic);
}else{
$new = $new+count($reply)+count($topic)-count($isRead);
}

$old = $old+count($reply)+count($topic)-$new-count($isHidden);
$total = $total+count($reply)+count($topic)-count($isHidden);
$favorite = count($isStared);

  if(isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'on'){
  $img = str_replace($_SERVER['DOCUMENT_ROOT'], "https://".$_SERVER['SERVER_NAME'], $img);
  }else{
  $img = str_replace($_SERVER['DOCUMENT_ROOT'], "http://".$_SERVER['SERVER_NAME'], $img);
  }
  if($sessionTrip){
      $mark = array("NotiState"=>"","NotiStr"=>"");
      $mark = array("NotiState_1"=>"","NotiStr_1"=>"");
      $star = array("FavState"=>"","FavStr"=>"");
      $star = array("FavState_1"=>"","FavStr_1"=>"");
  $out ='';
    if($new > 0){
        $bellIcon = HTML_PLUGIN_DIR.$plugin.DS."assets".DS."bell_notify.png";
    }else{
        $bellIcon = HTML_PLUGIN_DIR.$plugin.DS."assets".DS."bell.png";
    }
  $out .= '<div class="icon" id="bell"><img src="'.$bellIcon.'" alt="bell_icon"/></div>
   <div class="notifications" id="box">
        <h2>Notifications - <span>'.$new.'</span> new | <span>'.$old.'</span> old | <span>'.$total.'</span> total | <span>'.$favorite.'</span> stared</h2>';
        foreach($reply as $r){
            if(file_exists($db_hide.$r)){
                $hidden = "hidden=true";
            }else{
                $hidden = "";
            }
        # upload to extract JSON
        $data = strval(file_get_contents($replys.$r));
        $data = str_replace("<?php exit;?>", "", $data);
        $data = str_replace(array("\n","\r","\r\n"),"",$data);
        $data = preg_replace("/\s+/", "", $data);
        $d  = fopen($db_reply.$r, "w+");
        fwrite($d,"<?php "."$"."replyQuery = ".$data." ?>");
        fclose($d);
       # Create JSON
        $removePlugN = str_replace($_SERVER['DOCUMENT_ROOT'],"",dirname(__FILE__));
        $removePlugN = str_replace("plugin/Notification","view.php", $removePlugN);

       include $db_reply.$r;
       $selTop = $replyQuery['topic'];
       include $db_topic.$replyQuery['topic'].".dat.php";
       if(file_exists($db_read.$r)){
           $mark['NotiState'] = "1";
           $mark['NotiStr'] = "Mark as Unread";
       }else{
            $mark['NotiState'] = "0";
            $mark['NotiStr'] = "Mark as Read";
       }
       if(file_exists($db_star.$r)){
           $mark['FavState'] = "1";
           $mark['FavStr'] = "Unstared";
       }else{
           $mark['FavState'] = "0";
           $mark['FavStr'] = "Stared";
       }
        if($mark['NotiState'] === "1"){
            if($mark['FavState'] === "1"){
 $out .= '<div class="notifications-item" '.$hidden.' style="background-color:rgba(173,173,173,0.5);"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
            <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'&url='.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$r."&session=".$remote.'">Hide</a></p>
            </div>';
            }else{
 $out .= '<div class="notifications-item" '.$hidden.' style="background-color:rgba(173,173,173,0.5);"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'&url='.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$r."&session=".$remote.'">Hide</a></p>
            </div>';
            }
        }else{
            if($mark['FavState'] === "1"){
 $out .= '<div class="notifications-item" '.$hidden.'> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
             <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'&url='.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$r."&session=".$remote.'">Hide</a></p>
            </div>';
            }else{
 $out .= '<div class="notifications-item" '.$hidden.'> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'&url='.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$r."&session=".$remote.'">Hide</a></p>
            </div>';
            }
        }
               
       $out.='</div>';


       
    }
     foreach($topic as $t){
           if(file_exists($db_hide.$t)){
                $hidden = "hidden=true";
            }else{
                $hidden = "";
            }
        # upload to extract JSON
        $data1 = strval(file_get_contents($topics.$t));
        $data1 = str_replace("<?php exit;?>", "", $data1);
        $data1 = str_replace(array("\n","\r","\r\n"),"",$data1);
        $data1 = preg_replace("/\s+/", "", $data1);
        $d1  = fopen($db_topic.$t, "w+");
        fwrite($d1,"<?php "."$"."topicQuery = ".$data1." ?>");
        fclose($d1);

       # Create JSON
    $selRep = str_replace(".dat.php", "", $t);
       include $db_topic.$t;
       $removePlugD = str_replace($_SERVER['DOCUMENT_ROOT'],"",dirname(__FILE__));
        $removePlugD = str_replace("plugin/Notification","view.php", $removePlugD);
         if(file_exists($db_read.$t)){
           $mark['NotiState_1'] = "1";
           $mark['NotiStr_1'] = "Mark as Unread";
       }else{
            $mark['NotiState_1'] = "0";
            $mark['NotiStr_1'] = "Mark as Read";
       }
        if(file_exists($db_star.$t)){
           $mark['FavState_1'] = "1";
           $mark['FavStr_1'] = "Unstared";
       }else{
           $mark['FavState_1'] = "0";
           $mark['FavStr_1'] = "Stared";
       }
            if($mark['NotiState_1'] === "1"){
                if($mark['FavState_1'] === "1"){
$out .= '<div class="notifications-item" '.$hidden.' style="background-color:rgba(173,173,173,0.5) ;"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
                $out .= ' <div class="text">
                <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$r.'&session='.$remote.'&url='.$removePlugD.'/topic/'.$selRep.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$t."&session=".$remote.'">Hide</a></p>
            </div>';
                }else{
         $out .= '<div class="notifications-item" '.$hidden.' style="background-color:rgba(173,173,173,0.5);"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png"              alt="img">';
                $out .= ' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'&url='.$removePlugD.'/topic/'.$selRep.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$t."&session=".$remote.'">Hide</a></p>
            </div>';     
                }
            }else{
                if($mark['FavState_1'] === "1"){
  $out .= '<div class="notifications-item" '.$hidden.'> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
                $out.=' <div class="text">
                 <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'&url='.$removePlugD.'/topic/'.$selRep.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$t."&session=".$remote.'">Hide</a></p>
            </div>';
                }else{
                     $out .= '<div class="notifications-item" '.$hidden.'> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
                $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'&url='.$removePlugD.'/topic/'.$selRep.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?h=true'.'&r='.$t."&session=".$remote.'">Hide</a></p>
            </div>'; 
                }
               
            }
          
      $out.= '</div>';

    }
    $out.= '</div>';
                       
                                        

   

}else{
    $out .= 'You are not logged in, to view notifactions';
}
  return $out;
}
}
function Notification_head(){
    $plugin = 'Notification';
    $data = flatDB::readEntry('plugin', $plugin);
    if($data[$plugin.'state']){
   $out='';
    $out .= '<link rel="stylesheet" href="'.HTML_PLUGIN_DIR.$plugin.DS."assets".DS."Notification.css?ver=2.0.0".'"/>';
    return $out;
    }
}
function Notification_footerJS(){
        $plugin = 'Notification';
    $data = flatDB::readEntry('plugin', $plugin);
      if($data[$plugin.'state']){
    $out = '';
    $out .= "<script>$(document).ready(function(){




var down = false;

$('#bell').click(function(e){
    //getDevice
const deviceType = () => {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
        return 'tablet';
    }
    else if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
        return 'mobile';
    }
    return 'desktop';
};
let getDevice = deviceType();
if(getDevice === 'desktop'){
var color = $(this).text();
if(down){

$('#box').css('height','0px');
$('#box').css('opacity','0');
$('#box').css('overflow-y','auto');
down = false;
}else{

$('#box').css('height','1000%');
$('#box').css('opacity','1');
$('#box').css('overflow-y','auto');
down = true;
}

}else{
    var color = $(this).text();
if(down){

$('#box').css('height','0px');
$('#box').css('opacity','0');
$('#box').css('overflow-y','auto');
down = false;
}else{

$('#box').css('height','100%');
$('#box').css('opacity','1');
$('#box').css('overflow-y','auto');
down = true;
}

}



});


});</script>";
return $out;
      }
}
?>