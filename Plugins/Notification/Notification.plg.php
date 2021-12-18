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
  $img = UPLOADS_DIR."avatars".DS;
  $replys = DATA_DIR."reply".DS;
  $topics = DATA_DIR."topic".DS;
  $reply = array_values(array_diff(scandir($replys), array('..', '.')));
 $topic = array_values(array_diff(scandir($topics), array('..', '.')));


 $db_reply = PLUGIN_DIR.$plugin.DS."db".DS."reply".DS;
 $db_topic = PLUGIN_DIR.$plugin.DS."db".DS."topic".DS;
 $db_user = PLUGIN_DIR.$plugin.DS."db".DS."userList".DS;
 
 $remote = str_replace("@","_",$sessionTrip);
 if(!is_dir($db_user.$remote)){
   mkdir($db_user.$remote, 0777);
 }
 if(!is_dir($db_user.$remote.DS."readList")){
      mkdir($db_user.$remote.DS."readList", 0777);
 }
 if(!is_dir($db_user.$remote.DS."stared")){
      mkdir($db_user.$remote.DS."stared", 0777);
 }

    $db_read = PLUGIN_DIR.$plugin.DS."db".DS."userList".DS.$remote.DS."readList".DS;
    $db_star = PLUGIN_DIR.$plugin.DS."db".DS."userList".DS.$remote.DS."stared".DS;

 $isRead = array_values(array_diff(scandir($db_read), array('..', '.')));

$i=0;
if(count($isRead) === 0 || !count($isRead)){
$i = $i+count($reply)+count($topic);
}else{
$i = $i+count($reply)+count($topic)-count($isRead);
}



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
    if($i > 0){
        $bellIcon = HTML_PLUGIN_DIR.$plugin.DS."assets".DS."bell_notify.png";
    }else{
        $bellIcon = HTML_PLUGIN_DIR.$plugin.DS."assets".DS."bell.png";
    }
  $out .= '<div class="icon" id="bell"><img src="'.$bellIcon.'" alt="bell_icon"/></div>
   <div class="notifications" id="box">
        <h2>Notifications - <span>'.$i.'</span></h2>';
        foreach($reply as $r){
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
 $out .= '<div class="notifications-item" style="background-color:rgba(173,173,173,0.5) !important;"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
            <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a></p>
            </div>';
            }else{
 $out .= '<div class="notifications-item" style="background-color:rgba(173,173,173,0.5) !important;"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a></p>
            </div>';
            }
        }else{
        $out .= '<div class="notifications-item"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
            $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Replayed to: <a href="'.$removePlugN.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState'].'&r='.$r.'&session='.$remote.'">'.$mark['NotiStr'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState']."&r=".$r."&session=".$remote.'">'.$mark['FavStr'].'</a></p>
            </div>';
        }
               
       $out.='</div>';


       
    }
     foreach($topic as $t){
        # upload to extract JSON
        $data1 = strval(file_get_contents($topics.$t));
        $data1 = str_replace("<?php exit;?>", "", $data1);
        $data1 = str_replace(array("\n","\r","\r\n"),"",$data1);
        $data1 = preg_replace("/\s+/", "", $data1);
        $d1  = fopen($db_topic.$t, "w+");
        fwrite($d1,"<?php "."$"."topicQuery = ".$data1." ?>");
        fclose($d1);

       # Create JSON

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
$out .= '<div class="notifications-item" style="background-color:rgba(173,173,173,0.5) !important;"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
                $out .= ' <div class="text">
                <i class="fas fa-star"></i>
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.$removePlugD.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a></p>
            </div>';
                }else{
         $out .= '<div class="notifications-item" style="background-color:rgba(173,173,173,0.5) !important;"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png"              alt="img">';
                $out .= ' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.$removePlugD.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a></p>
            </div>';     
                }
            }else{
                 $out .= '<div class="notifications-item"> <img src="'.$img.str_replace("@","_",$replyQuery['trip']).'.png" alt="img">';
                $out.=' <div class="text">
                <h4>'.$replyQuery['trip'].'</h4>
                <p>Created Topic: <a href="'.$removePlugD.'/topic/'.$selTop.'">'.$topicQuery['title'].'<a></p>
                <p><a href="'.HTML_PLUGIN_DIR.$plugin.DS.'state.php?s='.$mark['NotiState_1'].'&r='.$t.'&session='.$remote.'">'.$mark['NotiStr_1'].'</a> | <a href="'.HTML_PLUGIN_DIR.$plugin.DS."fav.php?s=".$mark['FavState_1']."&r=".$t."&session=".$remote.'">'.$mark['FavStr_1'].'</a></p>
            </div>';
            }
               
      $out.= '</div>';

    }
    $out.= '</div>';
                                     /*    
             <div class="notifications-item"> <img src="https://img.icons8.com/flat_round/64/000000/vote-badge.png" alt="img">
                <div class="text">
                <h4>John Silvester</h4>
                <p>+20 vista badge earned</p>
            </div>
        </div>*/
                                        

   

}else{
    $out .= 'You are not logged in, to view notifactions';
}
  return $out;
}
function Notification_head(){
    $plugin = 'Notification';
    $out='';
    $out .= '<link rel="stylesheet" href="'.HTML_PLUGIN_DIR.$plugin.DS."assets".DS."Notification.css?ver=1.0.12".'"/>';
    return $out;
}
function Notification_footerJS(){
    $out = '';
    $out .= "<script>$(document).ready(function(){




var down = false;

$('#bell').click(function(e){

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

});

});</script>";
return $out;
}
?>