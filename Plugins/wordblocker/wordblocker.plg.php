<?php defined('FLATBOARD') or die('Flatboard Community.');

function wordblocker_install()
{
	global $lang;
	$plugin = 'wordblocker';
	if (flatDB::isValidEntry('plugin', $plugin))
		return;

    $data[$plugin.'state']   	  	= false;
	$data['bannedwords']   			= [];  
    $data['replacement']            = '';
	flatDB::saveEntry('plugin', $plugin, $data);
}
function wordblocker_config(){
	 global $lang, $token; 
    $plugin = 'wordblocker';
    $out ='';
    if(User::isAdmin()){
          if(!empty($_POST) && CSRF::check($token)){
               $data[$plugin.'state']= Util::isPOST('state') ? $_POST['state'] : ''; 
               $data['bannedwords']  = preg_split('(\n|\,)',HTMLForm::clean($_POST['bannedWords'])); 
               $data['replacement'] = HTMLForm::clean($_POST['replace']);    
               flatDB::saveEntry('plugin', $plugin, $data);
               $out .= Plugin::redirectMsg($lang['data_save'],'config.php' . DS . 'plugin' . DS . $plugin, $lang['plugin'].'&nbsp;<b>' .$lang[$plugin.'name']. '</b>');
       }else{
           if (flatDB::isValidEntry('plugin', $plugin))
              $words = '';
               $data = flatDB::readEntry('plugin', $plugin);
               foreach($data['bannedwords'] as $bw){
                   $words .= $bw;
               }
          $out .=  HTMLForm::form('config.php' . DS . 'plugin' . DS . $plugin, '
           <div class="row">
           <div class="col">
           '.HTMLForm::checkBox('state', $data[$plugin.'state']).'
           </div>
           </div>
           <div class="row">
           <div class="col">
           <textarea class="custom-form-control form-control" style="height:200px" name="bannedWords" placeholder="Enter Banned Words, use \'enter\' or \',\' to split words">'.$words.'</textarea>
           </div>
           </div>
           <br/>
         <div class="row">
           <div class="col">
           <input type="text" class="custom-form-control form-control" name="replace" placeholder="Enter replacement"/>
           </div>
           </div>
           <br/>'.HTMLForm::simple_submit());
       }
       return $out;
    }
}
function wordblocker_footerJS(){
    global $lang;
    $plugin = 'wordblocker';
    $out = '';
    $query = '';
     if (flatDB::isValidEntry('plugin', $plugin))
               $data = flatDB::readEntry('plugin', $plugin);
  if($data[$plugin.'state'] && count($data['bannedwords']) > 0){
       $out.='<script>';
       $out.='setTimeout(function(){
           let media = document.querySelectorAll(".media-body");
           for(i=0;i<media.length;i++){
               
               media[i].innerHTML = media[i].innerHTML.replace(';
              foreach($data['bannedwords'] as $key => $bw){
                  $bw = str_replace(" ", "", $bw);
                  if($key+1 == count($data['bannedwords'])){
                    $query.=$bw;
                  }else{    
                    $query.= $bw.'|';
                  }
              }
        $out .= '/('.preg_replace("/\r|\n|\s/", "", $query).')/gi';
     $out.=', '.($data['replacement']!=='' ? "\"".$data['replacement']."\"" : "\"\"").');
           }
          }, 0);';
       $out.='</script>';
       return $out;
     }
  
}
?>